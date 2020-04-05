<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

class RecordValueTypeConverter
{
    public static function convertSqlValueToPHP(Table $table, AbstractColumnField $field, $value) {
        if (!isset($table->__getColumn2TypeMap()[$field->getColumnName()])) {
            throw new \Error('"' . get_class($table) . '" __getColumn2TypeMap() entry for column "' . $field->getColumnName() . '" missing!');
        }

        if($value === null) {
            return null;
        }

        // todo: What about null?
        switch ($table->__getColumn2TypeMap()[$field->getColumnName()]) {
            case 'string':
                return $value;
                break;
            case 'integer':
                return (int)$value;
                break;
            case 'date':
                return new \DateTime($value);
                break;
            case 'datetime':
                return new \DateTime($value);
                break;
            case 'unknown':
                return $value;
                break;
            default:
                throw new \Error('Unsupported column type "' . $table->__getColumn2TypeMap()[$field->getColumnName()] . '"!');
        }
    }
}