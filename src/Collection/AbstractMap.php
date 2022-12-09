<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\Collection;

abstract class AbstractMap extends \ArrayObject
{
    public function __construct($input = array(), $flags = 0, $iterator_class = "ArrayIterator")
    {
        foreach ($input as $key => $value) {
            $this->validateKey($key);
            $this->validateValue($value);
        }
        parent::__construct($input, $flags, $iterator_class);
    }

    private function validateKey($value)
    {
        $type = $this->getKeyElementType();
        if(!$type->isTypeOfValueValid($value)) {
            throw new \InvalidArgumentException($type->getName().
                "-List is not allowed to contain keys of type \"".gettype($type));
        }
    }

    abstract function getKeyElementType() : KeyableVariableType;

    private function validateValue($value)
    {
        $type = $this->getValueElementType();
        if(!$type->isTypeOfValueValid($value)) {
            throw new \InvalidArgumentException($type->getName().
                "-List is not allowed to contain values of type \"".gettype($type));
        }
    }

    abstract function getValueElementType() : VariableType;

    public function offsetGet(mixed $key): mixed
    {
        $this->validateKey($key);
        return parent::offsetGet($key);
    }

    public function offsetSet(mixed $key, mixed $newval): void
    {
        $this->validateKey($key);
        $this->validateValue($newval);
        parent::offsetSet($key, $newval);
    }

    public function offsetExists(mixed $key): bool
    {
        $this->validateKey($key);
        return parent::offsetExists($key);
    }

    public function append(mixed $value): void
    {
        if($this->getKeyElementType() instanceof VariableTypeInteger) {
            $this->validateValue($value);
            parent::append($value);
        } else {
            throw new \Error('Map is not allowed to be used as list (except if its key-type is of type integer)!');
        }
    }
}