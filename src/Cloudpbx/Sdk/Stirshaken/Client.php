<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Stirshaken;

interface Client
{
    /**
     * @return Ip
     */
    public function getIps();

    /**
     * @return Certificate
     */
    public function getCertificates();

    /**
     * @return ApiKey
     */
    public function getApiKeys();

    /**
     * @return Cdr
     */
    public function getCdrs();

    /**
     * @return Provider
     */
    public function getProviders();
}
