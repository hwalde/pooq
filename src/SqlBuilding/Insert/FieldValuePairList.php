<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\SqlBuilding\Insert;

use POOQ\AbstractColumnField;
use POOQ\Collection\AbstractList;
use POOQ\Collection\VariableType;
use POOQ\Collection\VariableTypeInstance;

class FieldValuePairList extends AbstractList
{
    function getListElementType(): VariableType
    {
        return new VariableTypeInstance(FieldValuePair::class);
    }

    public function containsField(AbstractColumnField $field) : bool
    {
        return $this->getByField($field) !== null;
    }

    public function getByField(AbstractColumnField $field) : ?FieldValuePair
    {
        /** @var FieldValuePair $entry */
        foreach ($this as $entry) {
            if($field->getFieldName() === $entry->getField()->getFieldName()
                && $field->getModelName() === $entry->getField()->getModelName()) {
                return $entry;
            }
        }
    }
}