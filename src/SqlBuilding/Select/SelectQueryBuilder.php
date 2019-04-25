<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\SqlBuilding\Select;

use POOQ\Condition;
use POOQ\Field;
use POOQ\FieldList;
use POOQ\FieldOrTable;
use POOQ\Order;
use POOQ\Result;
use POOQ\ResultList;
use POOQ\Table;
use POOQ\TableAlias;

class SelectQueryBuilder implements SelectSelectPart, SelectFromPart, SelectMainPart, SelectOnPart
{
    /** @var string */
    private $sql;

    /** @var SelectField[]|SelectFieldList */
    private $selectFieldList;

    /**
     * @param FieldOrTable[]|string ...$fieldOrTableList Can be an instance of FieldOrTable or a fully qualified name to such class
     * @return SelectFromPart
     */
    function select(...$fieldOrTableList) : SelectFromPart
    {
        if(count($fieldOrTableList)==0) {
            throw new \Error('select clause is empty!');
        }
        $this->sql = 'SELECT ';
        $this->selectFieldList = SelectFieldList::generateFromFieldList(
            $this->convertToFieldList($fieldOrTableList)
        );
        $list = [];
        foreach ($this->selectFieldList as $field) {
            $list[] = $field->getSql();
        }
        $this->sql .= implode(', ', $list);
        return $this;
    }

    /**
     * @param FieldOrTable[] $fieldOrTableList
     * @return Field[]
     */
    private function convertToFieldList(array $fieldOrTableList): FieldList
    {
        $list = new FieldList();
        foreach ($fieldOrTableList as $fieldOrTable) {
            if(is_string($fieldOrTable)) {
                $fieldOrTable = new $fieldOrTable();
            }
            if ($fieldOrTable instanceof Field) {
                $list[] = $fieldOrTable;
            } else if ($fieldOrTable instanceof Table) {
                foreach ($fieldOrTable->__getFieldList() as $field) {
                    $list[] = $field;
                }
            } else {
                throw new \InvalidArgumentException('Expected instance of "'.FieldOrTable::class.'"!');
            }
        }
        return $list;
    }

    /**
     * @param string|Table|TableAlias $table Either the fully qualified name or the instance of a class implementing the table interface
     * @return SelectMainPart
     */
    public function from($table): SelectMainPart
    {
        $this->sql .= ' FROM '.$this->getQuotedTableName($table);
        return $this;
    }

    /**
     * @param string|Table|TableAlias $table Either the fully qualified name or the instance of a class implementing the table interface
     */
    private function getQuotedTableName($table): string
    {
        if(is_string($table)) {
            $table = new $table();
        }
        if(is_object($table) && $table instanceof TableAlias) {
            return $table->getTableName().' '.$table->getAliasName();
        } else if(is_object($table) && $table instanceof Table) {
            return $table->getTableName();
        } else {
            throw new \InvalidArgumentException('Expected instance of "'.FieldOrTable::class.'"!');
        }
    }

    /**
     * @param string|Table $table Either the fully qualified name or the instance of a class implementing the table interface
     * @return SelectOnPart
     */
    public function innerJoin($table): SelectOnPart
    {
        $this->sql .= ' INNER JOIN '.$this->getQuotedTableName($table);
        return $this;
    }

    public function leftJoin($table): SelectOnPart
    {
        $this->sql .= ' LEFT JOIN '.$this->getQuotedTableName($table);
        return $this;
    }

    public function on(Condition $condition): SelectMainPart
    {
        $this->sql .= ' ON '.$condition->toSql();
        return $this;
    }

    public function where(Condition $condition): SelectOrderPart
    {
        $this->sql .= ' WHERE '.$condition->toSql();
        return $this;
    }

    public function offset(int $offset): SelectEndPart
    {
        $this->sql .= ' OFFSET '.$offset;
        return $this;
    }

    public function order(Order $order): SelectLimitPart
    {
        $this->sql .= ' ORDER BY '.$order->toSql();
        return $this;
    }

    public function limit(int $limit): SelectOffsetPart
    {
        $this->sql .= ' LIMIT '.$limit;
        return $this;
    }

    public function fetch(): Result
    {
        $row = Database()->selectRow($this->sql);
        return new Result($this->selectFieldList, $row);
    }

    public function fetchAll(): ResultList
    {
        $list = new ResultList();
        $rows = Database()->selectAll($this->sql);
        foreach ($rows as $row) {
            $list[] = new Result($this->selectFieldList, $row);
        }
        return $list;
    }

    public function getSql(): string
    {
        return $this->sql;
    }
}