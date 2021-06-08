<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Http;

interface Request
{
    /**
     * @return string | null
     */
    public function body();

    /**
     * @return array<string, mixed>
     */
    public function headers();

    /**
     * @return string
     */
    public function method();

    /**
     * @return string
     */
    public function url();
}
