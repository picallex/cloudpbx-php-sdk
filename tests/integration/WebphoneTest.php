<?php

/**
 * Copyright 2026 Picallex Holding Group. All rights reserved.
 *
 * @author (2026) Agustin Serra <agustin@picallex.com>
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Cloudpbx\Sdk\Webphone;
use Cloudpbx\Sdk\Model\Webphone\ExtensionAvailability;
use Cloudpbx\Sdk\Model\Webphone\ExtensionAvailabilityList;

final class WebphoneTest extends TestCase
{
    private const DOMAIN = 'test.myflexpbx.com';

    /** @var array<string,mixed> */
    private $extensionData;

    /** @var array<string,mixed> */
    private $listResponse;

    protected function setUp(): void
    {
        $this->extensionData = [
            'domain'       => self::DOMAIN,
            'extension'    => '1001',
            'fqdn'         => '1001@test.myflexpbx.com',
            'registered'   => true,
            'transferable' => true,
        ];

        $this->listResponse = [
            'data' => [
                $this->extensionData,
                [
                    'domain'       => self::DOMAIN,
                    'extension'    => '1002',
                    'fqdn'         => '1002@test.myflexpbx.com',
                    'registered'   => false,
                    'transferable' => false,
                ],
            ],
            'pagination' => ['limit' => 20, 'offset' => 0, 'total' => 2],
            'summary'    => ['transferable' => 1, 'not_transferable' => 1],
        ];
    }

    private function makeWebphone(array $stubOne, array $stubListRaw): Webphone
    {
        $protocol = $this->createMock(\Cloudpbx\Sdk\Protocol::class);

        $protocol->method('prepareQuery')->willReturnArgument(0);

        if ($stubOne) {
            $protocol->method('one')->willReturn($stubOne);
        }

        if ($stubListRaw) {
            $protocol->method('listRaw')->willReturn($stubListRaw);
        }

        $webphone = new Webphone();
        $ref = new \ReflectionProperty(Webphone::class, 'protocol');
        $ref->setAccessible(true);
        $ref->setValue($webphone, $protocol);

        return $webphone;
    }

    private function makeWebphoneForGet(): Webphone
    {
        return $this->makeWebphone($this->extensionData, []);
    }

    private function makeWebphoneForList(): Webphone
    {
        return $this->makeWebphone([], $this->listResponse);
    }

    // --- getExtensionAvailability ---

    public function testGetExtensionAvailabilityReturnsModel(): void
    {
        $result = $this->makeWebphoneForGet()->getExtensionAvailability(self::DOMAIN, '1001');

        $this->assertInstanceOf(ExtensionAvailability::class, $result);
    }

    public function testGetExtensionAvailabilityMapsFields(): void
    {
        $result = $this->makeWebphoneForGet()->getExtensionAvailability(self::DOMAIN, '1001');

        $this->assertEquals(self::DOMAIN, $result->domain);
        $this->assertEquals('1001', $result->extension);
        $this->assertEquals('1001@test.myflexpbx.com', $result->fqdn);
        $this->assertTrue($result->registered);
        $this->assertTrue($result->transferable);
    }

    public function testGetExtensionAvailabilityHasAllAttributes(): void
    {
        $result = $this->makeWebphoneForGet()->getExtensionAvailability(self::DOMAIN, '1001');

        $this->assertTrue($result->hasAttribute('domain'));
        $this->assertTrue($result->hasAttribute('extension'));
        $this->assertTrue($result->hasAttribute('fqdn'));
        $this->assertTrue($result->hasAttribute('registered'));
        $this->assertTrue($result->hasAttribute('transferable'));
    }

    // --- listExtensionAvailability ---

    public function testListExtensionAvailabilityReturnsList(): void
    {
        $result = $this->makeWebphoneForList()->listExtensionAvailability(['domain' => self::DOMAIN]);

        $this->assertInstanceOf(ExtensionAvailabilityList::class, $result);
    }

    public function testListExtensionAvailabilityDataIsArrayOfModels(): void
    {
        $result = $this->makeWebphoneForList()->listExtensionAvailability(['domain' => self::DOMAIN]);

        $this->assertCount(2, $result->data);
        $this->assertInstanceOf(ExtensionAvailability::class, $result->data[0]);
        $this->assertInstanceOf(ExtensionAvailability::class, $result->data[1]);
    }

    public function testListExtensionAvailabilityPagination(): void
    {
        $result = $this->makeWebphoneForList()->listExtensionAvailability(['domain' => self::DOMAIN]);

        $this->assertEquals(20, $result->pagination['limit']);
        $this->assertEquals(0, $result->pagination['offset']);
        $this->assertEquals(2, $result->pagination['total']);
    }

    public function testListExtensionAvailabilitySummary(): void
    {
        $result = $this->makeWebphoneForList()->listExtensionAvailability(['domain' => self::DOMAIN]);

        $this->assertEquals(1, $result->summary['transferable']);
        $this->assertEquals(1, $result->summary['not_transferable']);
    }

    public function testListExtensionAvailabilityMapsItemFields(): void
    {
        $result = $this->makeWebphoneForList()->listExtensionAvailability(['domain' => self::DOMAIN]);

        $first = $result->data[0];
        $this->assertEquals(self::DOMAIN, $first->domain);
        $this->assertEquals('1001', $first->extension);
        $this->assertTrue($first->registered);
        $this->assertTrue($first->transferable);

        $second = $result->data[1];
        $this->assertEquals('1002', $second->extension);
        $this->assertFalse($second->registered);
        $this->assertFalse($second->transferable);
    }

    public function testListExtensionAvailabilityEmptyResponse(): void
    {
        $empty = [
            'data'       => [],
            'pagination' => ['limit' => 20, 'offset' => 0, 'total' => 0],
            'summary'    => ['transferable' => 0, 'not_transferable' => 0],
        ];

        $result = $this->makeWebphone([], $empty)->listExtensionAvailability(['domain' => self::DOMAIN]);

        $this->assertCount(0, $result->data);
        $this->assertEquals(0, $result->pagination['total']);
    }

    public function testListExtensionAvailabilityInvalidTransferableThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->makeWebphoneForList()->listExtensionAvailability([
            'domain'       => self::DOMAIN,
            'transferable' => 'invalid',
        ]);
    }

    public function testListExtensionAvailabilityMissingDomainThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->makeWebphoneForList()->listExtensionAvailability([]);
    }
}
