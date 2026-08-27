<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

/**
 * Catalogo global de gateways: recorre los endpoints de management y, por
 * cada uno, sus gateways root. Devuelve un catalogo plano para resolver
 * gateway_id -> nombre/realm (lo que el Switch Manager necesita).
 */
final class Gateway extends Api
{
    /**
     * @return array<Model\Gateway>
     */
    public function all()
    {
        $gateways = [];

        foreach ($this->listEndpoints() as $endpoint) {
            $endpoint_id = (int) $endpoint['id'];

            foreach ($this->listRootGateways($endpoint_id) as $record) {
                $gateways[] = $this->recordToModel(
                    $record,
                    Model\Gateway::class,
                    [
                        'transform' => [
                            function (&$record, $endpoint_id) {
                                $record['endpoint_id'] = $endpoint_id;
                            },
                            [$endpoint_id]
                        ]
                    ]
                );
            }
        }

        return $gateways;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function listEndpoints(): array
    {
        return $this->protocol->list(
            $this->protocol->prepareQuery('/api/v1/management/endpoints')
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function listRootGateways(int $endpoint_id): array
    {
        return $this->protocol->list(
            $this->protocol->prepareQuery(
                '/api/v1/root/endpoints/{endpoint_id}/gateways',
                ['{endpoint_id}' => $endpoint_id]
            )
        );
    }
}
