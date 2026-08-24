<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class CalleridReputationSetting extends \Cloudpbx\Sdk\Model
{
    /**
     * @var false fila unica de settings, sin id expuesto
     */
    public $id = false;

    /** @var bool master switch del sync */
    public $enabled;

    /** @var int 1..150 */
    public $batch_size;

    /** @var int */
    public $pause_between_batches_ms;

    /** @var bool corrida diaria automatica */
    public $scheduler_enabled;

    /** @var int 0..23, hora UTC de la corrida diaria */
    public $run_at_utc_hour;

    public function __construct()
    {
    }
}
