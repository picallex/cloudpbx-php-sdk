<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model\Webphone;

final class ExtensionAvailabilityList
{
    /**
     * @var array<ExtensionAvailability>
     */
    public $data;

    /**
     * @var array<string, int>
     */
    public $pagination;

    /**
     * @var array<string, int>
     */
    public $summary;

    /**
     * @param array<ExtensionAvailability> $data
     * @param array<string, int> $pagination
     * @param array<string, int> $summary
     */
    public function __construct(array $data, array $pagination, array $summary)
    {
        $this->data = $data;
        $this->pagination = $pagination;
        $this->summary = $summary;
    }
}
