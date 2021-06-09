<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util;

abstract class Api
{
    /**
     * @var \Cloudpbx\Sdk\Protocol
     */
    protected $protocol;

    // https://phpstan.org/blog/solving-phpstan-error-unsafe-usage-of-new-static
    final public function __construct()
    {
    }

    /**
     * @param \Cloudpbx\Sdk\Protocol $protocol
     *
     * @return static
     */
    public static function fromTransport($protocol)
    {
        Util\Argument::isInstanceOf($protocol, \Cloudpbx\Sdk\Protocol::class);

        $obj = new static();
        $obj->protocol = $protocol;
        return $obj;
    }

    /**
     * @param mixed $record
     * @param string $model
     * @return mixed
     */
    protected function recordToModel($record, $model)
    {
        Util\Argument::isClass($model);
        return $model::fromArray($record);
    }

    /**
     * @param array<mixed> $records
     * @param string $model
     * @return array<mixed>
     */
    protected function recordsToModel($records, $model)
    {
        Util\Argument::isClass($model);

        return array_map(
            function ($record) use ($model) {
                return $model::fromArray($record);
            },
            $records
        );
    }
}
