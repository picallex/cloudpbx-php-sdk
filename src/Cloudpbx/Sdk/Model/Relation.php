<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

use Cloudpbx\Util\Argument;

final class Relation
{
    /**
     * @var string
     */
    public $model;

    /**
     * @var integer
     */
    public $id;

    /**
     * @param string $model
     * @param integer $id
     */
    public function __construct($model, $id)
    {
        Argument::isString($model);
        Argument::isInteger($id);

        $this->model = $model;
        $this->id = $id;
    }
}
