<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model\Stirshaken;

final class ApiKey extends \Cloudpbx\Sdk\Model
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $key_preview;

    /**
     * raw key: solo se devuelve al crear, nunca mas.
     *
     * @var string|null
     */
    public $raw_key;

    /**
     * @var string|null ISO-8601
     */
    public $last_used_at;

    /**
     * @var string|null ISO-8601
     */
    public $created_at;

    public function __construct()
    {
    }
}
