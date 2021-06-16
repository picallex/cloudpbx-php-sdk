<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Protocol\Http\Implementation;

use PHPUnit\Framework\TestCase;

class RequestFromArrayTest extends TestCase
{

    function testProperties(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        RequestFromArray::build('GET', ['unknown' => 'field']);
    }
}
