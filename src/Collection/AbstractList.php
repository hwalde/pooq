<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\Collection;

abstract class AbstractList extends \ArrayObject
{
    public function __construct($input = array(), $flags = 0, $iterator_class = "ArrayIterator")
    {
        foreach ($input as $value) {
            $this->validateValue($value);
        }
        parent::__construct($input, $flags, $iterator_class);
    }

    private function validateValue($value)
    {
        $type = $this->getListElementType();
        if(!$type->isTypeOfValueValid($value)) {
            throw new \InvalidArgumentException($type->getName().
                "-List is not allowed to contain elements of type \"".gettype($type));
        }
    }

    abstract function getListElementType() : VariableType;

    public function offsetSet($index, $newval)
    {
        $this->validateValue($newval);
        parent::offsetSet($index, $newval);
    }

    public function append($value)
    {
        $this->validateValue($value);
        parent::append($value);
    }
}