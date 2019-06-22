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

    public function __construct(Result $result, Table $table)
    {
        $this->result = $result;
        $this->table = $table;
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

    private function setValue($value, ColumnField $field, Record $recordClass): void
    {
        $reflectionClass = new \ReflectionClass($recordClass);
        $property = $reflectionClass->getProperty($field->getFieldName());
        $property->setAccessible(true);

        /** @var RecordValue $recordValueObject */
        $recordValueObject = $property->getValue($recordClass);
        $recordValueObject->setHasBeenLoadedFromDatabase(true);

        $updatedValue = RecordValueTypeConverter::convertSqlValueToPHP($this->table, $field, $value);
        $recordValueObject->setValue($updatedValue);
        $recordValueObject->setOriginalValue($updatedValue);

        $property->setValue($recordClass, $recordValueObject);
    }
}