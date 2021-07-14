<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
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
     * @param array{'transform'?: array{0: Callable, 1: array<mixed>} } $options
     *
     * @return mixed
     */
    protected function recordToModel($record, $model, $options = [])
    {
        Util\Argument::isClass($model);
        $transform = $this->getTransform($options);

        $this->applyTransform($record, $transform);
        return $model::fromArray($record);
    }

    /**
     * @param array<mixed> $records
     * @param string $model
     * @param array{'transform'?: array{0: Callable, 1: array<mixed>} } $options
     *
     * @return array<mixed>
     */
    protected function recordsToModel($records, $model, $options = [])
    {
        Util\Argument::isClass($model);
        $transform = $this->getTransform($options);

        return array_map(
            function ($record) use ($model, $transform) {
                $this->applyTransform($record, $transform);
                return $model::fromArray($record);
            },
            $records
        );
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return Callable
     */
    private function getTransform($options = [])
    {
        if (!array_key_exists('transform', $options)) {
            return function ($record) {
            };
        }

        list($cb, $args) = $options['transform'];

        return function (&$record) use ($cb, $args) {
            call_user_func_array($cb, array_merge([&$record], $args));
        };
    }

    /**
     * @param array<string, mixed> $record
     * @param Callable $transform
     *
     * @return void
     */
    private function applyTransform(&$record, $transform)
    {
        call_user_func_array($transform, [&$record]);
    }
}
