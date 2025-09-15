<?php

/**
 * Copyright 2025 Picallex Holding Group. All rights reserved.
 *
 * @author (2025) Matias Damian Gomez <matias@picallex.com>
 */

declare(strict_types=1);

require_once('ClientTestCase.php');

use Cloudpbx\Sdk\Model\Customer\StatusReportDid;

class StatusReportDidTest extends ClientTestCase
{
    protected function localSetUp(): void
    {
        $this->customer = $this->createDefaultCustomer();
    }

    public function testCreateCustomerWithMinimalData(): array
    {
        $domain = $this->generateRandomDomain();
        $name = $this->generateRandomString(5);

        $customer = $this->client->customers->create([
            'name' => $name,
            'domain' => $domain
        ]);

        $did = 123456789;

        $statusDid = $this->client->customers->status_report_did($customer->id, $did);

        $this->assertInstanceOf(StatusReportDid::class, $statusDid);
        $this->assertTrue($statusDid->hasAttribute('id'));
        $this->assertTrue($statusDid->hasAttribute('callerid_info'));
        $this->assertTrue($statusDid->hasAttribute('customer_id'));
        $this->assertTrue($statusDid->hasAttribute('did_destination'));
        $this->assertTrue($statusDid->hasAttribute('follow_me'));
        $this->assertTrue($statusDid->hasAttribute('follow_me_entries'));

        return [$statusDid];
    }
}
