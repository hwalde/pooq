<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

use POOQ\Exception\FieldHasNotBeenSelectedException;
use POOQ\SqlBuilding\Select\SelectField;
use POOQ\SqlBuilding\Select\SelectFieldList;
use POOQ\Exception\SelectFieldNotFoundException;

class Result
{

    /** @var $selectFieldList SelectFieldList|SelectField[] */
    private $selectFieldList;

    /** @var array */
    private $databaseRow;

    public function __construct(SelectFieldList $fieldList, array $databaseRow)
    {
        $this->selectFieldList = $fieldList;
        $this->databaseRow = $databaseRow;
    }

    public function has(string $name): bool
    {
        return isset($this->databaseRow[$name]);
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \POOQ\Exception\SelectFieldNotFoundException
     */
    public function get(string $name) {
        if(!isset($this->databaseRow[$name])) {
            throw new SelectFieldNotFoundException($name);
        }
        return $this->databaseRow[$name];
    }

    /**
     * @param Field $field
     * @return bool
     */
    public function hasByField(Field $field) {
        $sql = $field->toSql();
        foreach ($this->selectFieldList as $selectField) {
            if($selectField->getField()->toSql() == $sql) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Field $field
     * @return mixed
     * @throws FieldHasNotBeenSelectedException
     */
    public function getByField(Field $field) {
        $sql = $field->toSql();
        foreach ($this->selectFieldList as $selectField) {
            if($selectField->getField()->toSql() == $sql) {
                return $this->get($selectField->getAliasName());
            }
        }
        throw new FieldHasNotBeenSelectedException();
    }

    /**
     * @param string|Table|TableAlias $table Either the fully qualified name or the instance of a class implementing the table interface
     * @return mixed
     * @throws SelectFieldNotFoundException
     */
    public function into($table) {
        if(is_string($table)) {
            $table = new $table();
        }
        if(!$table instanceof Table) {
            throw new \InvalidArgumentException('Expecting $table to be of type Table!');
        }
        $recordMapper = new RecordMapper($this, $table);
        return $recordMapper->generateRecord();
    }

}