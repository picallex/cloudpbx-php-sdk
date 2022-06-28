<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util;

/**
 * Model representa un recurso remoto.
 *
 * @property-read int $id
 * @property-read string $name
 */
abstract class Model
{
    /**
     * @var mixed
     */
    public $id;

    /**
     * @var string
     */
    protected $_primary_key = 'id';

    abstract public function __construct();

    /**
     * @param array<string, mixed> $metadata
     * @return static
     */
    public static function fromArray(array $metadata)
    {
        $obj = new static();

        if ($obj->id !== false && $obj->_primary_key !== null) {
            Util\Argument::keyExists($metadata, $obj->_primary_key);
            $obj->id = $metadata[$obj->_primary_key];
        }

        // populate only public fields
        $reflect = new \ReflectionClass($obj);
        $properties = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            $field_name = $property->getName();
            if (key_exists($field_name, $metadata)) {
                $obj->$field_name = $metadata[$field_name];
            }
        }

        $obj->setup();

        return $obj;
    }


    /**
     * allow setup model
     *
     * @return void
     */
    protected function setup()
    {
    }

    /**
     * Determinar existencia de atributo.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasAttribute($name)
    {
        return property_exists($this, $name);
    }
}
