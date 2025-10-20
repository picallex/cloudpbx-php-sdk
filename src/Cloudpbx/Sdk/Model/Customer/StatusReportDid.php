<?php

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model\Customer;

final class StatusReportDid extends \Cloudpbx\Sdk\Model
{
    /** @var int */
    public $id;

    /** @var array */
    public $callerid_info;

    /** @var int */
    public $customer_id;

    /** @var object */
    public $did_destination;

    /** @var object */
    public $follow_me;

    /** @var array */
    public $follow_me_entries;

    public function __construct($data = [])
    {
        $this->callerid_info = $data['callerid_info'] ?? [];
        $this->customer_id = (int)($data['customer_id'] ?? 0);
        $this->did_destination = (object)($data['did_destination'] ?? []);
        $this->id = (int)($data['did_id'] ?? 0);
        $this->follow_me = (object)($data['follow_me'] ?? []);
        $this->follow_me_entries = $data['follow_me_entries'] ?? [];
    }
}
