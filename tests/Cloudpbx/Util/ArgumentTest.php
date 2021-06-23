<?php
// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Cloudpbx\Util\Argument;

class ArgumentTest extends TestCase
{
    public function testChoices(): void
    {
        $this->expectExceptionMessage("value GETO invalid expected one of GET,POST,PUT,DELETE");

        Argument::choice('GETO', ['GET', 'POST', 'PUT', 'DELETE']);
    }
}
