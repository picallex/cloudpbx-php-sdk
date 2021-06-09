<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Util;

final class Argument
{
    /**
     * @param array<mixed, mixed> $array
     * @param mixed $key
     *
     * @return void
     */
    public static function keyWithValue($array, $key)
    {
        self::assert(!is_null($array[$key]), "array not has '{$key}' with valid value");
    }

    /**
     * @param array<mixed, mixed> $array
     * @param mixed $key
     *
     * @return void
     */
    public static function keyExists($array, $key)
    {
        self::assert(array_key_exists($key, $array), "not found key '{$key}' in array");
    }

    /**
     * @param mixed $value
     * @param array<mixed> $choices
     *
     * @return void
     */
    public static function choice($value, $choices)
    {
        self::assert(in_array($value, $choices), "value {$value} invalid expected one of ${implode(',', $choices)}");
    }

    /**
     * @param mixed $value
     *
     * @return void
     */
    public static function isArray($value)
    {
        self::assert(gettype($value) === "array", 'expected array');
    }

    /**
     * @param mixed $value
     *
     * @return void
     */
    public static function isInteger($value)
    {
        self::assert(gettype($value) === "integer", 'expected integer');
    }

    /**
     * @param mixed $value
     *
     * @return void
     */
    public static function isString($value)
    {
        self::assert(gettype($value) === "string", 'expected string');
    }

    /**
     *
     * @param mixed $value
     * @param mixed $klass
     *
     * @return void
     */
    public static function isInstanceOf($value, $klass)
    {
        self:assert($value instanceof $klass, "expected instance of {$klass}");
    }

    /**
     * @param mixed $value
     * @param mixed $message
     *
     * @throws \InvalidArgumentException
     * @return void
     */
    private static function assert($value, $message): void
    {
        if ($value === false) {
            throw new \InvalidArgumentException($message);
        }
    }
}
