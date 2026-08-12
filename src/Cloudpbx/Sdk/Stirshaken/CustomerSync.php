<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Stirshaken;

use Cloudpbx\Util\Argument;

/**
 * Sincroniza los prefijos de salida de los customers de cloudpbx (myflexpbx)
 * hacia las IPs de la plataforma stir/shaken.
 *
 * Regla de origen:
 *  - IP  = resolucion DNS del domain del customer.
 *  - prefijo = campo `prepend` de cada dialout group; si el customer no tiene
 *    dialout groups con prepend, se cae a los dialouts.
 *  - source_ref = 'dialout_group:{dialout_id}:{group_id}' | 'dialout:{id}'.
 *
 * Reconciliacion (estado deseado = cloudpbx, actual = stir):
 *  - create  : prefijo nuevo.
 *  - update  : prefijo existente cuyo destino cambio (o reactivacion).
 *  - skip    : sin cambios (evita invalidar el cache de Kamailio al pedo).
 *  - soft_delete : IP administrada por el sync cuyo source_ref ya no existe en
 *    cloudpbx -> se marca is_active=false (no se borra).
 *
 * La config es por-customer (default global + override), inyectada por quien
 * orquesta (p.ej. picallex-admin) via el resolver de syncAllCustomers().
 * Claves de config: certificate_id, provider_id, provider_weight,
 * call_direction (default 'outbound'), forwarding_mode (default 'redirect'),
 * enabled (default true), dry_run (default false).
 */
final class CustomerSync
{
    /**
     * @var \Cloudpbx\Sdk\Client
     */
    private $cloudpbx;

    /**
     * @var \Cloudpbx\Sdk\Stirshaken\Client
     */
    private $stir;

    /**
     * @var callable(string): ?string
     */
    private $resolveIp;

    /**
     * @param \Cloudpbx\Sdk\Client $cloudpbx
     * @param \Cloudpbx\Sdk\Stirshaken\Client $stir
     * @param null|callable(string): ?string $resolveIp
     */
    public function __construct($cloudpbx, $stir, $resolveIp = null)
    {
        $this->cloudpbx = $cloudpbx;
        $this->stir = $stir;
        $this->resolveIp = $resolveIp ?? function ($domain) {
            $ip = gethostbyname($domain);
            return $ip === $domain ? null : $ip;
        };
    }

    /**
     * Sincroniza un solo customer.
     *
     * @param int $customer_id
     * @param array<string, mixed> $config
     *
     * @return array<string, mixed>
     */
    public function syncCustomer($customer_id, $config = [])
    {
        Argument::isInteger($customer_id);
        Argument::isArray($config);

        $customer = $this->cloudpbx->customers->show($customer_id);
        $all_ips = $this->stir->ips->all();

        return $this->reconcileCustomer($customer, $config, $all_ips);
    }

    /**
     * Sincroniza TODOS los customers. El resolver devuelve la config efectiva
     * de cada customer (override ?? global); si devuelve ['enabled'=>false] se
     * saltea ese customer.
     *
     * $options: dry_run (bool), delay_ms (int, pausa entre customers),
     *           limit (int, 0 = todos).
     *
     * @param callable(mixed): array<string,mixed> $configResolver
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    public function syncAllCustomers($configResolver, $options = [])
    {
        Argument::isArray($options);

        $delay_ms = (int)($options['delay_ms'] ?? 0);
        $limit = (int)($options['limit'] ?? 0);
        $dry_run = (bool)($options['dry_run'] ?? false);

        $customers = $this->cloudpbx->customers->all();
        if ($limit > 0) {
            $customers = array_slice($customers, 0, $limit);
        }
        $all_ips = $this->stir->ips->all();

        $results = [];
        $totals = ['create' => 0, 'update' => 0, 'skip' => 0, 'soft_delete' => 0, 'errors' => 0];

        foreach ($customers as $customer) {
            $config = $configResolver($customer);
            $config['dry_run'] = $dry_run;

            if (isset($config['enabled']) && !$config['enabled']) {
                $results[] = ['customer_id' => $customer->id, 'skipped' => 'disabled', 'actions' => [], 'errors' => []];
                continue;
            }

            $r = $this->reconcileCustomer($customer, $config, $all_ips);
            $results[] = $r;

            foreach ($r['actions'] as $a) {
                if (isset($totals[$a['action']])) {
                    $totals[$a['action']]++;
                }
            }
            $totals['errors'] += count($r['errors']);

            if ($delay_ms > 0) {
                usleep($delay_ms * 1000);
            }
        }

        return ['customers' => count($results), 'totals' => $totals, 'results' => $results];
    }

    /**
     * Nucleo de reconciliacion para un customer.
     *
     * @param mixed $customer
     * @param array<string, mixed> $config
     * @param array<mixed> $all_ips  IPs actuales de stir (todas)
     *
     * @return array<string, mixed>
     */
    private function reconcileCustomer($customer, $config, $all_ips)
    {
        $cid = (int)$customer->id;
        $dry_run = (bool)($config['dry_run'] ?? false);
        // los dialouts son SALIENTES -> por defecto outbound + proxy (igual que el
        // script sync_sipproxy_ips original). outbound exige certificate_id.
        $call_direction = $config['call_direction'] ?? 'outbound';
        $forwarding_mode = $config['forwarding_mode'] ?? 'proxy';
        $certificate_id = $config['certificate_id'] ?? null;
        $cps_limit = (int)($config['cps_limit'] ?? 10);
        // label = lo que va antes del primer punto del domain (ej: bpinatural.myflexpbx.com -> bpinatural)
        $label = $customer->domain ? strtok((string)$customer->domain, '.') : '';

        $summary = ['customer_id' => $cid, 'domain' => $customer->domain, 'ip' => null, 'actions' => [], 'errors' => []];

        $ip = $customer->domain ? ($this->resolveIp)($customer->domain) : null;
        $summary['ip'] = $ip;
        if (!$ip) {
            $summary['errors'][] = 'no se pudo resolver la IP del domain del customer';
            return $summary;
        }

        // IPs administradas por el sync para ESTE customer, indexadas por source_ref.
        $managed = [];
        foreach ($all_ips as $existing) {
            if ((int)$existing->cloudpbx_customer_id === $cid && !empty($existing->source_ref)) {
                $managed[$existing->source_ref] = $existing;
            }
        }

        $desired = $this->collectPrefixes($cid);

        // create / update / skip
        foreach ($desired as $source_ref => $prefix) {
            $want = [
                'ip_cidr' => $ip,
                'description' => "{$label}-{$prefix}",
                'customer_prefix' => $prefix,
                'call_direction' => $call_direction,
                'forwarding_mode' => $forwarding_mode,
                'cps_limit' => $cps_limit,
                'cloudpbx_customer_id' => $cid,
                'source_ref' => $source_ref,
                'is_active' => true,
            ];
            if ($certificate_id !== null) {
                $want['certificate_id'] = $certificate_id;
            }

            try {
                $current = $managed[$source_ref] ?? null;
                if ($current === null) {
                    $model = $dry_run ? null : $this->stir->ips->create($want);
                    $this->addAction($summary, 'create', $source_ref, $prefix, $model);
                    $ip_id = $model !== null ? $model->id : null;
                } elseif ($this->hasChanged($current, $want)) {
                    $model = $dry_run ? $current : $this->stir->ips->update($current->id, $want);
                    $this->addAction($summary, 'update', $source_ref, $prefix, $model);
                    $ip_id = $current->id;
                } else {
                    $this->addAction($summary, 'skip', $source_ref, $prefix, $current);
                    $ip_id = $current->id;
                }

                if ($ip_id !== null && !$dry_run) {
                    $this->reconcileProvider($ip_id, $config, $summary, $source_ref);
                }
            } catch (\Throwable $e) {
                $summary['errors'][] = "create/update {$source_ref}: " . $e->getMessage();
            }
        }

        // soft-delete: IPs administradas cuyo source_ref ya no esta en cloudpbx.
        foreach ($managed as $source_ref => $existing) {
            if (isset($desired[$source_ref]) || !$existing->is_active) {
                continue;
            }
            try {
                if (!$dry_run) {
                    $this->stir->ips->update($existing->id, $this->ipParamsFrom($existing, ['is_active' => false]));
                }
                $this->addAction($summary, 'soft_delete', $source_ref, $existing->customer_prefix, $existing);
            } catch (\Throwable $e) {
                $summary['errors'][] = "soft_delete {$source_ref}: " . $e->getMessage();
            }
        }

        return $summary;
    }

    /**
     * Asignacion idempotente del proveedor configurado a la IP.
     * Si no hay provider_id en la config, no toca los proveedores.
     *
     * @param string $ip_id
     * @param array<string, mixed> $config
     * @param array<string, mixed> $summary
     * @param string $source_ref
     *
     * @return void
     */
    private function reconcileProvider($ip_id, $config, &$summary, $source_ref)
    {
        $provider_id = $config['provider_id'] ?? null;
        if ($provider_id === null) {
            return;
        }
        $weight = (int)($config['provider_weight'] ?? 100);

        $found = null;
        foreach ($this->stir->ips->providers($ip_id) as $a) {
            if ($a->provider_id === $provider_id) {
                $found = $a;
            } else {
                $this->stir->ips->unassignProvider($ip_id, $a->id);
                $summary['actions'][] = ['action' => 'provider_remove', 'source_ref' => $source_ref, 'ip_id' => $ip_id];
            }
        }

        if ($found === null) {
            $this->stir->ips->assignProvider($ip_id, ['provider_id' => $provider_id, 'weight' => $weight]);
            $summary['actions'][] = ['action' => 'provider_assign', 'source_ref' => $source_ref, 'ip_id' => $ip_id];
        } elseif ((int)$found->weight !== $weight) {
            $this->stir->ips->updateProvider($ip_id, $found->id, ['weight' => $weight]);
            $summary['actions'][] = ['action' => 'provider_update', 'source_ref' => $source_ref, 'ip_id' => $ip_id];
        }
    }

    /**
     * @param array<string, mixed> $summary
     * @param string $action
     * @param string $source_ref
     * @param string $prefix
     * @param mixed $model
     *
     * @return void
     */
    private function addAction(&$summary, $action, $source_ref, $prefix, $model)
    {
        $summary['actions'][] = [
            'action' => $action,
            'source_ref' => $source_ref,
            'prefix' => $prefix,
            'ip_id' => $model !== null ? $model->id : null,
        ];
    }

    /**
     * true si el destino deseado difiere del actual (dispara update).
     *
     * @param mixed $current
     * @param array<string, mixed> $want
     *
     * @return bool
     */
    private function hasChanged($current, $want)
    {
        $cur_ip = preg_replace('#/.*$#', '', (string)$current->ip_cidr);
        if ($cur_ip !== $want['ip_cidr']) {
            return true;
        }
        if ((string)$current->customer_prefix !== (string)$want['customer_prefix']) {
            return true;
        }
        if ($current->call_direction !== $want['call_direction']) {
            return true;
        }
        if ($current->forwarding_mode !== $want['forwarding_mode']) {
            return true;
        }
        if (!$current->is_active) {   // estaba soft-deleted -> reactivar
            return true;
        }
        if (isset($want['certificate_id']) && $current->certificate_id !== $want['certificate_id']) {
            return true;
        }
        return false;
    }

    /**
     * Construye el payload de update a partir de una IP existente + overrides.
     *
     * @param mixed $ip
     * @param array<string, mixed> $override
     *
     * @return array<string, mixed>
     */
    private function ipParamsFrom($ip, $override)
    {
        return array_merge([
            'ip_cidr' => preg_replace('#/.*$#', '', (string)$ip->ip_cidr),
            'description' => $ip->description,
            'customer_prefix' => $ip->customer_prefix,
            'call_direction' => $ip->call_direction,
            'forwarding_mode' => $ip->forwarding_mode,
            'cps_limit' => $ip->cps_limit,
            'cloudpbx_customer_id' => $ip->cloudpbx_customer_id,
            'source_ref' => $ip->source_ref,
            'certificate_id' => $ip->certificate_id,
            'is_active' => $ip->is_active,
        ], $override);
    }

    /**
     * Junta los prefijos del customer indexados por source_ref.
     * Prioriza dialout groups; si no hay ninguno con prepend, cae a dialouts.
     *
     * @param int $customer_id
     *
     * @return array<string, string> source_ref => prefix
     */
    private function collectPrefixes($customer_id)
    {
        $prefixes = [];

        foreach ($this->cloudpbx->dialoutGroups->all($customer_id) as $g) {
            if (!empty($g->prepend)) {
                $prefixes["dialout_group:{$g->dialout_id}:{$g->group_id}"] = (string)$g->prepend;
            }
        }

        if (!$prefixes) {
            foreach ($this->cloudpbx->dialouts->all($customer_id) as $d) {
                if (!empty($d->prepend)) {
                    $prefixes["dialout:{$d->id}"] = (string)$d->prepend;
                }
            }
        }

        return $prefixes;
    }
}
