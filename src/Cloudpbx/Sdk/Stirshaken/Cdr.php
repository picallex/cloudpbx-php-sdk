<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Stirshaken;

use Cloudpbx\Sdk\Model;

/**
 * Call Detail Records de la plataforma stir/shaken (solo lectura).
 */
final class Cdr extends \Cloudpbx\Sdk\Api
{
    /**
     * @return array<Model\Stirshaken\Cdr>
     */
    public function all()
    {
        // ponytail: sin filtros; el endpoint acepta query params (from/to/direction...),
        // agregar cuando haga falta paginar/filtrar desde el SDK.
        $query = $this->protocol->prepareQuery('/api/v1/cdrs');

        $records = $this->protocol->listRaw($query);

        return $this->recordsToModel($records, Model\Stirshaken\Cdr::class);
    }

    /**
     * Estadisticas agregadas. La api responde un objeto sin envelope "data";
     * se devuelve tal cual (no hay id para mapear a un Model).
     *
     * @return array<string, mixed>
     */
    public function stats()
    {
        $query = $this->protocol->prepareQuery('/api/v1/cdrs/stats');

        return $this->protocol->oneRaw($query);
    }
}
