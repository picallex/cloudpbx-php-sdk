<?php

/**
 * Copyright 2026 Picallex Holding Group. All rights reserved.
 *
 * @author (2026) Matias Gomez <matias@picallex.com>
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Cloudpbx\Sdk\Model\Webphone\AgentStatus;
use Cloudpbx\Sdk\Webphone;

final class WebphoneAgentStatusTest extends TestCase
{
    private const DOMAIN = 'test.myflexpbx.com';

    /** @var array<string,mixed> */
    private $busyData;

    protected function setUp(): void
    {
        $this->busyData = [
            'domain' => self::DOMAIN,
            'extension' => '1001',
            'fqdn' => '1001@test.myflexpbx.com',
            'registered' => true,
            'do_not_disturb' => true,
            'dnd_status' => 'Almuerzo',
            'callcenter_status' => 'logged_out',
        ];
    }

    public function testGetAgentStatusMapsFields(): void
    {
        $result = $this->makeWebphone(['one' => $this->busyData])
            ->getAgentStatus(self::DOMAIN, '1001');

        $this->assertInstanceOf(AgentStatus::class, $result);
        $this->assertEquals(self::DOMAIN, $result->domain);
        $this->assertEquals('1001', $result->extension);
        $this->assertEquals('1001@test.myflexpbx.com', $result->fqdn);
        $this->assertTrue($result->registered);
        $this->assertTrue($result->do_not_disturb);
        $this->assertEquals('Almuerzo', $result->dnd_status);
        $this->assertEquals('logged_out', $result->callcenter_status);
        $this->assertFalse($result->isAvailable());
    }

    public function testGetAgentStatusOfAvailableAgent(): void
    {
        $available = [
            'domain' => self::DOMAIN,
            'extension' => '1001',
            'fqdn' => '1001@test.myflexpbx.com',
            'registered' => true,
            'do_not_disturb' => false,
            'dnd_status' => null,
            'callcenter_status' => 'available',
        ];

        $result = $this->makeWebphone(['one' => $available])->getAgentStatus(self::DOMAIN, '1001');

        $this->assertTrue($result->isAvailable());
        $this->assertNull($result->dnd_status);
    }

    public function testSetAgentStatusSendsDomainAndStatusInTheBody(): void
    {
        $protocol = $this->createMock(\Cloudpbx\Sdk\Protocol::class);
        $protocol->method('prepareQuery')->willReturnArgument(0);

        $protocol->expects($this->once())
            ->method('update')
            ->with(
                '/api/v1/management/webphone/extensions/{extension}/agent-status',
                [
                    'domain' => self::DOMAIN,
                    'status' => 'busy',
                    'substatus' => 'Almuerzo',
                ]
            )
            ->willReturn($this->busyData + ['realtime_synced' => true]);

        $result = $this->webphoneWith($protocol)->setAgentStatus(self::DOMAIN, '1001', [
            'status' => 'busy',
            'substatus' => 'Almuerzo',
        ]);

        $this->assertEquals('Almuerzo', $result->dnd_status);
        $this->assertTrue($result->realtime_synced);
    }

    // el cambio se aplico igual; el llamador tiene que poder distinguirlo
    public function testSetAgentStatusExposesAFailedRealtimeMirror(): void
    {
        $result = $this->makeWebphone(['update' => $this->busyData + ['realtime_synced' => false]])
            ->setAgentStatus(self::DOMAIN, '1001', ['status' => 'busy']);

        $this->assertFalse($result->realtime_synced);
        $this->assertFalse($result->isAvailable());
    }

    public function testGetAgentStatusHasNoRealtimeMirrorFlag(): void
    {
        $result = $this->makeWebphone(['one' => $this->busyData])
            ->getAgentStatus(self::DOMAIN, '1001');

        $this->assertNull($result->realtime_synced);
    }

    public function testSetAgentStatusAvailableOmitsSubstatus(): void
    {
        $protocol = $this->createMock(\Cloudpbx\Sdk\Protocol::class);
        $protocol->method('prepareQuery')->willReturnArgument(0);

        $protocol->expects($this->once())
            ->method('update')
            ->with(
                $this->anything(),
                ['domain' => self::DOMAIN, 'status' => 'available']
            )
            ->willReturn(['do_not_disturb' => false, 'dnd_status' => null]);

        $result = $this->webphoneWith($protocol)->setAgentStatus(self::DOMAIN, '1001', [
            'status' => 'available',
        ]);

        $this->assertTrue($result->isAvailable());
    }

    public function testSetAgentStatusInterpolatesTheExtension(): void
    {
        $protocol = $this->createMock(\Cloudpbx\Sdk\Protocol::class);

        $protocol->expects($this->once())
            ->method('prepareQuery')
            ->with($this->anything(), ['{extension}' => '1001'])
            ->willReturn('/prepared');

        $protocol->method('update')->willReturn($this->busyData);

        $this->webphoneWith($protocol)->setAgentStatus(self::DOMAIN, '1001', ['status' => 'busy']);
    }

    public function testSetAgentStatusRejectsAnUnknownStatus(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->makeWebphone(['update' => $this->busyData])
            ->setAgentStatus(self::DOMAIN, '1001', ['status' => 'paused']);
    }

    public function testSetAgentStatusRequiresStatus(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->makeWebphone(['update' => $this->busyData])
            ->setAgentStatus(self::DOMAIN, '1001', []);
    }

    // una pausa sin ocupado no significa nada, y dejarla pasar en silencio
    // esconderia el error del llamador
    public function testSetAgentStatusRejectsSubstatusWhenAvailable(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->makeWebphone(['update' => $this->busyData])->setAgentStatus(self::DOMAIN, '1001', [
            'status' => 'available',
            'substatus' => 'Almuerzo',
        ]);
    }

    /**
     * @param array<string,mixed> $stubs
     */
    private function makeWebphone(array $stubs): Webphone
    {
        $protocol = $this->createMock(\Cloudpbx\Sdk\Protocol::class);

        $protocol->method('prepareQuery')->willReturnArgument(0);

        foreach ($stubs as $method => $value) {
            $protocol->method($method)->willReturn($value);
        }

        return $this->webphoneWith($protocol);
    }

    private function webphoneWith(object $protocol): Webphone
    {
        $webphone = new Webphone();
        $ref = new \ReflectionProperty(Webphone::class, 'protocol');
        $ref->setAccessible(true);
        $ref->setValue($webphone, $protocol);

        return $webphone;
    }
}
