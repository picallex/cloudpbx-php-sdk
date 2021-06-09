<?php
// Copyright 2021 Picallex Holding Group. All rights reserved.
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
