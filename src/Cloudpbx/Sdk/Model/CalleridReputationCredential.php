<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class CalleridReputationCredential extends \Cloudpbx\Sdk\Model
{
    /**
     * @var false la clave es customer_id
     */
    public $id = false;

    /** @var int */
    public $customer_id;

    /** @var bool */
    public $enabled;

    /**
     * enmascarado por el backend (ab12****yz89), nunca viene completo.
     *
     * @var string|null
     */
    public $api_key;

    public function __construct()
    {
    }
}
