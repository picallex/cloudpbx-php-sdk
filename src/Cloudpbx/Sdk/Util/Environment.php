<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Util;

final class Environment
{
    /**
     * obtener variable segun entorno.
     *
     * @param string $environment
     * @param string $name
     *
     * @throws \RuntimeException if not found environment variable
     * @return string
     */
    public static function get($environment, $name)
    {
        $root = realpath(join(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', '..', '..']));

        if ($root === false) {
            throw new \RuntimeException("can't found root dir");
        }

        $dotenv = \Dotenv\Dotenv::createImmutable($root, ".env.{$environment}");
        $dotenv->load();

        $value = $_ENV[$name] ?? false;

        if ($value === false) {
            throw new \RuntimeException("not found environment variable {$name}");
        }

        return $value;
    }
}
