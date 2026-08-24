<?php

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class NumberLookup extends \Cloudpbx\Sdk\Model
{
    /** @var string E.164 */
    public $number;

    /** @var string */
    public $carrier;

    /** @var string */
    public $carrier_key;

    /** @var string mobile|landline|voip|... */
    public $line_type;

    /** @var string */
    public $region;

    /** @var string provider used to resolve */
    public $provider;

    /** @var string ISO-8601 */
    public $queried_at;

    /**
     * full payload as returned by the api.
     *
     * @var array<string, mixed>
     */
    public $data;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct($data = [])
    {
        $this->number = (string)($data['number'] ?? '');
        $this->carrier = (string)($data['carrier'] ?? '');
        $this->carrier_key = (string)($data['carrier_key'] ?? '');
        $this->line_type = (string)($data['line_type'] ?? '');
        $this->region = (string)($data['region'] ?? '');
        $this->provider = (string)($data['provider'] ?? '');
        $this->queried_at = (string)($data['queried_at'] ?? '');
        $this->data = $data;
    }
}
