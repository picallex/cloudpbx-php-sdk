<?php

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class CdrTrace extends \Cloudpbx\Sdk\Model
{
    /** @var string */
    public $recorduuid;

    /** @var int */
    public $customer_id;

    /**
     * full trace payload as returned by the api under "data".
     *
     * @var array<string, mixed>
     */
    public $data;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct($data = [])
    {
        $this->recorduuid = (string)($data['recorduuid'] ?? '');
        $this->customer_id = (int)($data['customer_id'] ?? 0);
        $this->data = $data;
    }
}
