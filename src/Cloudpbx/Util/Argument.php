<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Util;

final class Argument
{
    /**
     * @param string $value
     *
     * @return void
     */
    public static function isClass($value)
    {
        self::assert(class_exists($value), "class '{$value}' not exists");
    }

    /**
     * @param array<mixed, mixed> $array
     * @param mixed $key
     *
     * @return void
     */
    public static function keyWithValue($array, $key)
    {
        @self::assert(!is_null($array[$key]), "array not has '{$key}' with valid value");
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
        self::assert(in_array($value, $choices), "value {$value} invalid expected one of ". implode(',', $choices));
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
    public static function isParams($value)
    {
        self::isArray($value);
        array_walk_recursive($value, function ($item, $key) {
            $value_type = gettype($item);
            $valid = in_array($value_type, ['integer', 'string', 'boolean']);
            self::assert($valid, "not allowed value of $value_type in key $key, only allows integer, string, boolean");
        });
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
    public static function isDigits($value)
    {
        self::assert(preg_match('/^\d+$/', (string)$value) === 1, 'expected digits');
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

    public static function isPath($value)
    {
        self::assert(file_exists($value) === true, "not found file at ${value}");
    }

    /**
     * @param mixed $value
     * @param string $assertion name of assertion
     * @param array<mixed> $rest
     *
     * @return bool
     */
    public static function optional($value, $assertion, ...$rest): bool
    {
        if (!is_null($value)) {
            self::$assertion(...array_merge([$value], $rest));
            return true;
        }

        return false;
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
     *
     * @param mixed $value
     * @param string $format
     *
     * @return void
     */
    public static function isFormat($value, $format)
    {
        self::assert(preg_match($format, $value), "expected format {$format}");
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
