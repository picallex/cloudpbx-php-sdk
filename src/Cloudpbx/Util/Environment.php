<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Util;

final class Environment
{
    /**
     * obtener variable segun entorno.
     *
     * @param string $environment
     * @param string $name
     * @param mixed $default
     *
     * @throws \RuntimeException if not found environment variable
     * @return string
     */
    public static function get($environment, $name, $default = null)
    {
        $root = realpath(join(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', '..']));

        if ($root === false) {
            throw new \RuntimeException("can't found root dir");
        }

        $dotenv = \Dotenv\Dotenv::createImmutable($root, ".env.{$environment}");
        $dotenv->load();

        $value = (string)($_ENV[$name] ?? ($default ?? ""));

        if ($value === "" && $default === null) {
            throw new \RuntimeException("not found environment variable {$name}");
        }

        return $value;
    }
}
