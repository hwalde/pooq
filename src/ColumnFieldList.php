<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

use POOQ\Collection\AbstractList;
use POOQ\Collection\VariableType;
use POOQ\Collection\VariableTypeInstance;

class ColumnFieldList extends AbstractList
{
    function getListElementType() : VariableType
    {
        return new VariableTypeInstance(ColumnField::class);
    }
}