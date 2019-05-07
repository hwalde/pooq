<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

use POOQ\Field\Functions\String\LengthFunction;

abstract class Field extends FieldOrValue implements FieldOrTable
{
    public abstract function getFieldName(): string;

    public function asc() : Order
    {
        return new OrderImpl($this->toSql().' ASC');
    }

    public function desc(): Order
    {
        return new OrderImpl($this->toSql().' DESC');
    }

    public function length(): LengthFunction
    {
        return new LengthFunction($this);
    }
}