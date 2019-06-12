<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

use POOQ\Exception\MissingPrimaryKeyValueException;
use POOQ\Exception\NotConnectedToDatabaseException;
use POOQ\SqlBuilding\Select\SelectFromPart;

abstract class AbstractUpdateableRecord implements UpdateableRecord
{
    /**
     * insert or update the record to the database
     * @return int The number of affected rows
     */
    public function store(): ?int
    {
        if($this->existsInDatabase()) {
            return $this->updateRecord();
        } else {
            return $this->insertRecord();
        }
    }

    /**
     * @param string $actionName
     * @throws MissingPrimaryKeyValueException
     */
    private function validatePrimaryKeyValuesExist(string $actionName): void
    {
        foreach ($this->__getModel()->__listPrimaryKeyColumns() as $columnName) {
            $recordClassName = get_class($this);
            $fieldName = $this->__getModel()->__getColumn2NameMap()[$columnName];
            /** @var RecordValue $recordValueObject */
            $recordValueObject = $this->{$fieldName};
            if(!$recordValueObject->hasBeenLoadedFromDatabase()) {
                $prefix = 'Cannot '.$actionName.' '.$recordClassName;
                throw new MissingPrimaryKeyValueException($prefix, $recordClassName, $fieldName);
            }
        }
    }

    private function existsInDatabase() : bool
    {
        foreach ($this->__getModel()->__getFieldList() as $columnField) {
            $fieldName = $this->__getModel()->__getColumn2NameMap()[$columnField->getColumnName()];
            /** @var RecordValue $recordValueObject */
            $recordValueObject = $this->{$fieldName};
            if($recordValueObject->hasBeenLoadedFromDatabase()) {
                return true;
            }
        }
        return false;
    }

    private function updateRecord(): int
    {
        $this->validatePrimaryKeyValuesExist('store');

        $updateQuery = update($this->__getModel());

        $nameMap = $this->__getModel()->__getColumn2NameMap();

        $hasChangedFields = false;
        foreach ($this->__getModel()->__getFieldList() as $columnField) {
            $fieldName = $nameMap[$columnField->getColumnName()];

            /** @var RecordValue $recordValueObject */
            $recordValueObject = $this->{$fieldName};
            if(!$recordValueObject->isChanged()) {
                continue;
            }

            $updateQuery = $updateQuery->set($columnField, $recordValueObject->getValue());
            $hasChangedFields = true;
        }

        if(!$hasChangedFields) {
            return 0;
        }

        return $updateQuery->where($this->getSqlWhereCondition())
            ->execute();
    }

    /**
     * @return Condition
     */
    private function getSqlWhereCondition(): Condition
    {
        $nameMap = $this->__getModel()->__getColumn2NameMap();

        /** @var Condition $condition */
        $condition = null;
        foreach ($this->__getModel()->__listPrimaryKeyColumns() as $columnName) {
            $fieldName = $nameMap[$columnName];
            $value = $this->{$fieldName}->getOriginalValue();
            /** @var Field $field */
            $field = $this->__getModel()->$fieldName();
            $newCondition = $field->eq(value($value));
            if ($condition === null) {
                $condition = $newCondition;
            } else {
                $condition = $condition->and($newCondition);
            }
        }
        return $condition;
    }

    private function insertRecord(): int
    {
        // todo: check that required fields are set (SQL NOT NULL & NO DEFAULT VALUE COLUMNS)

        $insertQuery = insertInto($this->__getModel());

        $nameMap = $this->__getModel()->__getColumn2NameMap();

        foreach ($this->__getModel()->__getFieldList() as $columnField) {
            $fieldName = $nameMap[$columnField->getColumnName()];

            /** @var RecordValue $recordValueObject */
            $recordValueObject = $this->{$fieldName};
            if($recordValueObject->hasBeenSet()) {
                $insertQuery = $insertQuery->set($columnField, $recordValueObject->getValue());
            }
        }

        $result = $insertQuery->execute();

        if(count($this->__getModel()->__listPrimaryKeyColumns())==1) {
            $pkColumnName = $this->__getModel()->__listPrimaryKeyColumns()[0];
            $pkFieldName = $nameMap[$pkColumnName];
            $pkField = $this->__getModel()->{$pkFieldName}();
            $this->setFieldValueAsLoadedFromDatabase($pkField, $result->getLastInsertId(), $pkFieldName);
        }

        return $result->getAffectedRowsCount();
    }

    /**
     * @param ColumnField $columnField
     * @param $value
     * @param $fieldName
     */
    private function setFieldValueAsLoadedFromDatabase(ColumnField $columnField, $value, $fieldName): void
    {
        $updatedValue = RecordValueTypeConverter::convertSqlValueToPHP($this->__getModel(), $columnField, $value);
        $this->{$fieldName}->setOriginalValue($updatedValue);
        $this->{$fieldName}->setValue($updatedValue);
        $this->{$fieldName}->setChanged(false);
        $this->{$fieldName}->setHasBeenLoadedFromDatabase(true);
    }

    public function refresh(): void
    {
        if(!$this->existsInDatabase()) {
            throw new NotConnectedToDatabaseException('This record was not loaded from database and therefor cannot be refreshed!');
        }

        $this->validatePrimaryKeyValuesExist('refresh');

        $fieldList = [];
        foreach ($this->__getModel()->__getFieldList() as $columnField) {
            $fieldName = $this->__getModel()->__getColumn2NameMap()[$columnField->getColumnName()];
            /** @var RecordValue $recordValueObject */
            $recordValueObject = $this->{$fieldName};
            if($recordValueObject->hasBeenSet()) {
                $fieldList[] = $columnField;
            }
        }

        /** @var SelectFromPart $select */
        $select = call_user_func_array('\POOQ\select', $fieldList);

        $result = $select
            ->from($this->__getModel())
            ->where($this->getSqlWhereCondition())
            ->fetch();
        foreach ($this->__getModel()->__getFieldList() as $columnField) {
            $value = $result->getByField($columnField);
            $fieldName = $this->__getModel()->__getColumn2NameMap()[$columnField->getColumnName()];
            $this->setFieldValueAsLoadedFromDatabase($columnField, $value, $fieldName);
        }
    }

    public function delete(): int
    {
        if(!$this->existsInDatabase()) {
            throw new NotConnectedToDatabaseException('This record was not loaded from database and therefor cannot be deleted!');
        }

        $this->validatePrimaryKeyValuesExist('delete');

        return delete($this->__getModel())
            ->where($this->getSqlWhereCondition())
            ->execute();
    }
}