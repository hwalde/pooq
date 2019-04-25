<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

use POOQ\Exception\SelectFieldNotFoundException;

class RecordMapper
{
    /** @var Result */
    private $result;

    /** @var Table */
    private $table;

    /** @var array */
    private $columnToNameMap;

    /** @var array */
    private $columnToTypeMap;

    public function __construct(Result $result, Table $table)
    {
        $this->result = $result;
        $this->table = $table;
        $this->columnToNameMap = $table->__getColumn2NameMap();
        $this->columnToTypeMap = $table->__getColumn2TypeMap();
    }


    /**
     * @throws SelectFieldNotFoundException
     * @return Record
     */
    public function generateRecord()
    {
        $recordClass = $this->table->__getRecordClass();
        foreach ($this->table->__getFieldList() as $field) {
            if(!$this->result->hasByField($field)) {
                continue;
            }
            $value = $this->result->getByField($field);
            $this->setValue($value, $field, $recordClass);
        }
        return $recordClass;
    }

    private function getSetterMethodName(ColumnField $field, Record $recordClass): string
    {
        if (!isset($this->columnToNameMap[$field->getColumnName()])) {
            throw new \Error('"' . get_class($recordClass) . '" __getColumn2NameMap() entry for column "' . $field->getColumnName() . '"!');
        }
        $camelCaseName = $this->columnToNameMap[$field->getColumnName()];
        $setterName = 'set' . strtoupper($camelCaseName[0]) . substr($camelCaseName, 1);
        return $setterName;
    }

    private function setValue($value, ColumnField $field, Record $recordClass): void
    {
        if (!isset($this->columnToTypeMap[$field->getColumnName()])) {
            throw new \Error('"' . get_class($recordClass) . '" __getColumn2TypeMap() entry for column "' . $field->getColumnName() . '"!');
        }

        $setterName = $this->getSetterMethodName($field, $recordClass);
        switch ($this->columnToTypeMap[$field->getColumnName()]) {
            case 'string':
                $recordClass->$setterName($value);
                break;
            case 'integer':
                $recordClass->$setterName((int)$value);
                break;
            case 'date':
                $recordClass->$setterName(new \DateTime($value));
                break;
            case 'datetime':
                $recordClass->$setterName(new \DateTime($value));
                break;
            case 'unknown':
                $recordClass->$setterName($value);
                break;
            default:
                throw new \Error('Unsupported column type "' . $this->columnToTypeMap[$field->getColumnName()] . '"!');
        }
    }
}