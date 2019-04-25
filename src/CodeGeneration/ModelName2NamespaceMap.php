<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\CodeGeneration;

use POOQ\Collection\AbstractMap;
use POOQ\Collection\KeyableVariableType;
use POOQ\Collection\VariableType;
use POOQ\Collection\VariableTypeInstance;
use POOQ\Collection\VariableTypeString;

class ModelName2NamespaceMap extends AbstractMap
{
    function getKeyElementType(): KeyableVariableType
    {
        return new VariableTypeString();
    }

    function getValueElementType(): VariableType
    {
        return new VariableTypeInstance(NamespaceObject::class);
    }
}