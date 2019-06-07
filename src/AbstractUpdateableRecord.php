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

abstract class AbstractUpdateableRecord implements UpdateableRecord
{
    /** @var bool */
    private $existsInDatabase;

    public function __construct(bool $existsInDatabase = false)
    {
        $this->existsInDatabase = $existsInDatabase;
    }

    /**
     * insert or update the record to the database
     */
    public function store(): ?int
    {
        $this->validatePrimaryKeyValuesExist('store');

        if($this->existsInDatabase) {
            return $this->updateRecord();
        } else {
            return $this->insertRecord();
        }
    }

    private function updateRecord(): ?int
    {
        $updateQuery = update($this->__getModel());

        $nameMap = $this->__getModel()->__getColumn2NameMap();

        // todo: get only changed fields
        $hasChangedFields = false;
        foreach ($this->__getModel()->__getFieldList() as $columnField) {
            $fieldName = $nameMap[$columnField->getColumnName()];
            $value = $this->$fieldName;
            $updateQuery = $updateQuery->set($columnField, $value);
            $hasChangedFields = true;
        }

        if($hasChangedFields) {
            return $updateQuery->where($this->getSqlWhereCondition())
                ->execute();
        }
    }

    private function insertRecord(): int
    {
        // todo: check that required fields are set

        $insertQuery = insertInto($this->__getModel());

        $nameMap = $this->__getModel()->__getColumn2NameMap();

        foreach ($this->__getModel()->__getFieldList() as $columnField) {
            $fieldName = $nameMap[$columnField->getColumnName()];
            $value = $this->$fieldName;
            $insertQuery = $insertQuery->set($columnField, $value);
        }

        $id = $insertQuery->execute();

        if(count($this->__getModel()->__listPrimaryKeyColumns())==1) {
            $pkColumnName = $this->__getModel()->__listPrimaryKeyColumns()[0];
            $pkFieldName = $nameMap[$pkColumnName];
            $this->$pkFieldName = $id;
        } else {
            $this->refresh(); // to update pk
        }

        return $id;
    }

    public function refresh(): void
    {
        if(!$this->existsInDatabase) {
            throw new NotConnectedToDatabaseException('This record was not loaded from database and therefor cannot be refreshed!');
        }

        $this->validatePrimaryKeyValuesExist('refresh');

        // todo: refresh only fields that are set in resultset? And what about null values?
        $result = select($this->__getModel())
            ->from($this->__getModel())
            ->where($this->getSqlWhereCondition())
            ->fetch();
        foreach ($this->__getModel()->__getFieldList() as $columnField) {
            $value = $result->getByField($columnField);
            $fieldName = $this->__getModel()->__getColumn2NameMap()[$columnField->getColumnName()];
            $this->$fieldName = $value;
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
            if($this->$fieldName === null) {
                $prefix = 'Cannot '.$actionName.' '.$recordClassName;
                throw new MissingPrimaryKeyValueException($prefix, $recordClassName, $fieldName);
            }
        }
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
            $value = $this->$fieldName;
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

    public function delete(): int
    {
        if(!$this->existsInDatabase) {
            throw new NotConnectedToDatabaseException('This record was not loaded from database and therefor cannot be deleted!');
        }

        $this->validatePrimaryKeyValuesExist('delete');

        return delete($this->__getModel())
            ->where($this->getSqlWhereCondition())
            ->execute();
    }
}