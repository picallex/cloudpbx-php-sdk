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
     * @var array<integer>
     */
    public $path_ids;

    /**
     * @param string $model
     * @param integer $id
     * @param array<integer> $path_ids
     */
    public function __construct($model, $id, $path_ids = [])
    {
        Argument::isString($model);
        Argument::isInteger($id);

        $this->model = $model;
        $this->id = $id;
        $this->path_ids = $path_ids;
    }
}
