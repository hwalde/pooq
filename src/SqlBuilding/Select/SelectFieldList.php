<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\SqlBuilding\Select;

use POOQ\Collection\AbstractList;
use POOQ\Collection\VariableType;
use POOQ\Collection\VariableTypeInstance;
use POOQ\ColumnField;
use function POOQ\Database;
use POOQ\Field;
use POOQ\FieldList;

class SelectFieldList extends AbstractList
{
    /**
     * @param $fieldList Field[]
     * @return SelectField[]
     */
    public static function generateFromFieldList(FieldList $fieldList): SelectFieldList
    {
        $list = [];

        // Handle aliased fields
        $nameToUsageCountMap = [];
        foreach ($fieldList as $field) {
            if ($field instanceof ColumnField) {
                continue;
            }
            $name = $field->getFieldName();
            if(isset($nameToUsageCountMap[$name])) {
                throw new \InvalidArgumentException('There is more than one field with the name"'.$name.'"!');
            }
            $nameToUsageCountMap[$name] = true;
            $list[] = new SelectField($field, $field->toSql(), $name);
        }

        // Handle non-aliased fields (columns)
        foreach ($fieldList as $field) {
            if (!$field instanceof ColumnField) {
                continue;
            }
            $usedName = $columnName = $field->getColumnName();
            $counter = 0;
            while (isset($nameToUsageCountMap[$usedName])) {
                $usedName = $columnName.$counter;
                $counter++;
            }
            $nameToUsageCountMap[$usedName] = true;
            $list[] = new SelectField($field, $field->toSql().' as '.Database()->quoteIdentifier($usedName), $usedName);
        }

        return new SelectFieldList($list);
    }

    function getListElementType() : VariableType
    {
        return new VariableTypeInstance(SelectField::class);
    }
}