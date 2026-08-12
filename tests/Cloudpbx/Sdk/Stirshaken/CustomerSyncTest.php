<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Cloudpbx\Protocol\ProtocolHTTP;
use Cloudpbx\Sdk\Implementation\Client as CloudpbxClient;
use Cloudpbx\Sdk\Stirshaken\Implementation\Client as StirClient;
use Cloudpbx\Sdk\Stirshaken\CustomerSync;
use Cloudpbx\Protocol\Http\Request;
use Cloudpbx\Protocol\Http\Response;
use Cloudpbx\Protocol\Http\Implementation\ResponseFromArray;

class CustomerSyncTest extends TestCase
{
    /**
     * transport fake que rutea por substring de URL (primer match gana).
     *
     * @param array<int, array{0: string, 1: string}> $routes
     * @return object
     */
    private function routingTransport(array $routes)
    {
        return new class ($routes) implements \Cloudpbx\Protocol\Http\Client {
            /** @var array<int, array{0: string, 1: string}> */
            private $routes;

            public function __construct($routes)
            {
                $this->routes = $routes;
            }

            /** @var string|null */
            public $last_post_body;

            public function sendRequest(Request $request): Response
            {
                if ($request->method() === 'POST') {
                    $this->last_post_body = $request->body();
                }
                foreach ($this->routes as [$needle, $body]) {
                    // el needle puede llevar prefijo de metodo: "POST /api/v1/ips"
                    if (strpos($needle, ' ') !== false) {
                        [$m, $u] = explode(' ', $needle, 2);
                        if ($request->method() === $m && strpos($request->url(), $u) !== false) {
                            return new ResponseFromArray($body, 200);
                        }
                        continue;
                    }
                    if (strpos($request->url(), $needle) !== false) {
                        return new ResponseFromArray($body, 200);
                    }
                }
                return new ResponseFromArray('[]', 200);
            }
        };
    }

    /**
     * @param object $transport
     * @param callable $resolveIp
     * @return CustomerSync
     */
    private function syncWith($transport, $resolveIp)
    {
        $cloudpbx = new CloudpbxClient(new ProtocolHTTP('https://cloudpbx.example', 'K', $transport));
        $stir = new StirClient(new ProtocolHTTP('https://stir.example', 'K', $transport));
        return new CustomerSync($cloudpbx, $stir, $resolveIp);
    }

    /** IP existente en stir (respuesta plana). @return array<string,mixed> */
    private function existingIp(array $over = [])
    {
        return array_merge([
            'id' => 'ip-1',
            'ip_cidr' => '203.0.113.77/32',
            'customer_prefix' => '8',
            'certificate_id' => null,
            'forwarding_mode' => 'redirect',
            'call_direction' => 'inbound',
            'cloudpbx_customer_id' => 123,
            'source_ref' => 'dialout:7',
            'is_active' => true,
        ], $over);
    }

    public function testCreatesFromDialoutGroupsDryRun(): void
    {
        $transport = $this->routingTransport([
            ['/dialout_groups', json_encode(['data' => [
                ['dialout_id' => 12, 'group_id' => 34, 'callerid_group_id' => 1, 'prepend' => '9'],
                ['dialout_id' => 12, 'group_id' => 35, 'callerid_group_id' => 1, 'prepend' => ''],
            ]])],
            ['/dialouts', json_encode(['data' => []])],
            ['/customers/', json_encode(['data' => ['id' => 123, 'domain' => 'cust.example.com']])],
            ['/api/v1/ips', '[]'],
        ]);
        $sync = $this->syncWith($transport, function ($d) {
            return '203.0.113.77';
        });

        $out = $sync->syncCustomer(123, ['dry_run' => true]);

        $this->assertEquals('203.0.113.77', $out['ip']);
        $this->assertEmpty($out['errors']);
        $this->assertCount(1, $out['actions']);
        $this->assertEquals('create', $out['actions'][0]['action']);
        $this->assertEquals('dialout_group:12:34', $out['actions'][0]['source_ref']);
        $this->assertEquals('9', $out['actions'][0]['prefix']);
    }

    public function testFallsBackToDialouts(): void
    {
        $transport = $this->routingTransport([
            ['/dialout_groups', json_encode(['data' => []])],
            ['/dialouts', json_encode(['data' => [['id' => 7, 'customer_id' => 123, 'prepend' => '8']]])],
            ['/customers/', json_encode(['data' => ['id' => 123, 'domain' => 'cust.example.com']])],
            ['/api/v1/ips', '[]'],
        ]);
        $sync = $this->syncWith($transport, function ($d) {
            return '203.0.113.77';
        });

        $out = $sync->syncCustomer(123, ['dry_run' => true]);

        $this->assertCount(1, $out['actions']);
        $this->assertEquals('create', $out['actions'][0]['action']);
        $this->assertEquals('dialout:7', $out['actions'][0]['source_ref']);
    }

    public function testSkipsWhenUnchanged(): void
    {
        // existe una IP identica al estado deseado -> skip (no invalida cache)
        $transport = $this->routingTransport([
            ['/dialout_groups', json_encode(['data' => []])],
            ['/dialouts', json_encode(['data' => [['id' => 7, 'customer_id' => 123, 'prepend' => '8']]])],
            ['/customers/', json_encode(['data' => ['id' => 123, 'domain' => 'cust.example.com']])],
            ['/api/v1/ips', json_encode([$this->existingIp()])],
        ]);
        $sync = $this->syncWith($transport, function ($d) {
            return '203.0.113.77';
        });

        $out = $sync->syncCustomer(123, ['call_direction' => 'inbound', 'forwarding_mode' => 'redirect', 'dry_run' => true]);

        $this->assertCount(1, $out['actions']);
        $this->assertEquals('skip', $out['actions'][0]['action']);
    }

    public function testSoftDeletesOrphan(): void
    {
        // en stir hay una IP administrada (dialout:99) que ya no existe en cloudpbx
        $transport = $this->routingTransport([
            ['/dialout_groups', json_encode(['data' => []])],
            ['/dialouts', json_encode(['data' => [['id' => 7, 'customer_id' => 123, 'prepend' => '8']]])],
            ['/customers/', json_encode(['data' => ['id' => 123, 'domain' => 'cust.example.com']])],
            ['/api/v1/ips', json_encode([
                $this->existingIp(['id' => 'ip-orphan', 'source_ref' => 'dialout:99', 'customer_prefix' => '5']),
            ])],
        ]);
        $sync = $this->syncWith($transport, function ($d) {
            return '203.0.113.77';
        });

        $out = $sync->syncCustomer(123, ['call_direction' => 'inbound', 'forwarding_mode' => 'redirect', 'dry_run' => true]);

        $actions = [];
        foreach ($out['actions'] as $a) {
            $actions[$a['action']] = $a['source_ref'];
        }
        $this->assertEquals('dialout:7', $actions['create']);        // el vigente se crea
        $this->assertEquals('dialout:99', $actions['soft_delete']);  // el huerfano se soft-deletea
    }

    public function testCreatePayloadIsOutboundProxyWithDescription(): void
    {
        // los dialouts son salientes: default outbound + proxy, description "{label}-{prefix}"
        $created = json_encode([
            'id' => 'new-ip', 'ip_cidr' => '203.0.113.77/32', 'customer_prefix' => '8',
            'call_direction' => 'outbound', 'forwarding_mode' => 'proxy', 'is_active' => true,
            'cloudpbx_customer_id' => 2, 'source_ref' => 'dialout:7',
        ]);
        $transport = $this->routingTransport([
            ['/dialout_groups', json_encode(['data' => []])],
            ['/dialouts', json_encode(['data' => [['id' => 7, 'customer_id' => 2, 'prepend' => '8']]])],
            ['/customers/', json_encode(['data' => ['id' => 2, 'domain' => 'localclient1.myflexpbx.com']])],
            ['POST /api/v1/ips', $created],
            ['/api/v1/ips', '[]'],
        ]);
        $sync = $this->syncWith($transport, function ($d) {
            return '203.0.113.77';
        });

        $sync->syncCustomer(2, ['certificate_id' => 'cert-1']);   // sin dry_run: crea de verdad

        $sent = json_decode($transport->last_post_body, true);
        $this->assertEquals('outbound', $sent['call_direction']);
        $this->assertEquals('proxy', $sent['forwarding_mode']);
        $this->assertEquals('localclient1-8', $sent['description']);
        $this->assertEquals('8', $sent['customer_prefix']);
        $this->assertEquals('cert-1', $sent['certificate_id']);
        $this->assertEquals('dialout:7', $sent['source_ref']);
    }

    public function testSyncsAllPrefixesIncludingHashSuffix(): void
    {
        // se sincronizan todos los prefijos (no se excluye '6975#1')
        $transport = $this->routingTransport([
            ['/dialout_groups', json_encode(['data' => []])],
            ['/dialouts', json_encode(['data' => [
                ['id' => 7, 'customer_id' => 2, 'prepend' => '6975#'],
                ['id' => 8, 'customer_id' => 2, 'prepend' => '6975#1'],
            ]])],
            ['/customers/', json_encode(['data' => ['id' => 2, 'domain' => 'c.example.com']])],
            ['/api/v1/ips', '[]'],
        ]);
        $sync = $this->syncWith($transport, function ($d) {
            return '203.0.113.77';
        });

        $out = $sync->syncCustomer(2, ['dry_run' => true]);

        $refs = array_column($out['actions'], 'source_ref');
        $this->assertContains('dialout:7', $refs);   // 6975#
        $this->assertContains('dialout:8', $refs);   // 6975#1 tambien entra
    }

    public function testSyncAllCustomersHonorsResolverAndEnabled(): void
    {
        $transport = $this->routingTransport([
            ['/dialout_groups', json_encode(['data' => []])],
            ['/dialouts', json_encode(['data' => [['id' => 7, 'customer_id' => 123, 'prepend' => '8']]])],
            ['/api/v1/management/customers', json_encode(['data' => [
                ['id' => 1, 'domain' => 'a.example.com'],
                ['id' => 2, 'domain' => 'b.example.com'],
            ]])],
            ['/api/v1/ips', '[]'],
        ]);
        $sync = $this->syncWith($transport, function ($d) {
            return '203.0.113.77';
        });

        // customer 2 deshabilitado via override
        $resolver = function ($c) {
            return $c->id === 2
                ? ['enabled' => false]
                : ['call_direction' => 'inbound', 'forwarding_mode' => 'redirect'];
        };

        $out = $sync->syncAllCustomers($resolver, ['dry_run' => true]);

        $this->assertEquals(2, $out['customers']);
        $this->assertEquals(1, $out['totals']['create']);           // solo el customer 1
        $this->assertEquals('disabled', $out['results'][1]['skipped']);
    }
}
