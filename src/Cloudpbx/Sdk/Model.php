<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

/**
 * Model representa un recurso remoto.
 *
 * @property-read int $id
 * @property-read string $name
 */
abstract class Model
{
    /**
     *@var int
     */
    protected $_id;

    /**
     *@var array<string, mixed>
     */
    protected $_metadata;

    abstract public function __construct();

    /**
     * @param array<string, mixed> $metadata
     * @return static
     */
    public static function fromArray(array $metadata)
    {
        Util\Argument::keyExists($metadata, 'id');

        $obj = new static();
        $obj->_id = intval($metadata['id']);
        $obj->_metadata = (new \ArrayObject($metadata))->getArrayCopy();

        return $obj;
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
        return array_key_exists($name, $this->_metadata);
    }

    /**
     * Acceder a los atributos dinamicamente
     *
     * @param string $name
     *
     * # ejemplo
     *
     * <code>
     *  $customer = Model\Customer::fromArray(['id' => 3]);
     *  $customer->id;
     * </code>
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->_metadata)) {
            return $this->_metadata[$name];
        }

        return null;
    }
}
