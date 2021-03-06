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
use function POOQ\Database;
use POOQ\Field;
use POOQ\FieldList;
use POOQ\FieldOrTable;
use POOQ\Order;
use POOQ\Result;
use POOQ\ResultList;
use POOQ\SqlBuilding\SqlBuildingHelperTrait;
use POOQ\Table;
use POOQ\TableAlias;

class SelectQueryBuilder implements SelectSelectPart, SelectFromPart, SelectMainPart, SelectOnPart
{
    use SqlBuildingHelperTrait;

    /** @var string */
    private $sql;

    /** @var SelectField[]|SelectFieldList */
    private $selectFieldList;

    /**
     * @param FieldOrTable[]|string ...$fieldOrTableList Can be an instance of FieldOrTable or a fully qualified name to such class
     * @return SelectFromPart
     */
    public function select(...$fieldOrTableList) : SelectFromPart
    {
        return $this->createSelectClause(false, ...$fieldOrTableList);
    }

    /**
     * @param FieldOrTable[]|string ...$fieldOrTableList Can be an instance of FieldOrTable or a fully qualified name to such class
     * @return SelectFromPart
     */
    public function selectDistinct(...$fieldOrTableList) : SelectFromPart
    {
        return $this->createSelectClause(true, ...$fieldOrTableList);
    }

    /**
     * @param bool $distinct Is it a SELECT DISTINCT query?
     * @param FieldOrTable[]|string ...$fieldOrTableList Can be an instance of FieldOrTable or a fully qualified name to such class
     * @return SelectFromPart
     */
    private function createSelectClause(bool $distinct, ...$fieldOrTableList) : SelectFromPart
    {
        if(count($fieldOrTableList)==0) {
            throw new \Error('select clause is empty!');
        }
        $this->sql = 'SELECT ';
        if($distinct) {
            $this->sql .= 'DISTINCT';
        }
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
     * Create an "SELECT COUNT(*) FROM ..." query
     * @return SelectFromPart
     */
    function selectCount(): SelectFromPart
    {
        $this->sql = 'SELECT COUNT(*)';
        return $this;
    }

    /**
     * @param string|Table|TableAlias $table Either the fully qualified name or the instance of a class implementing the table interface
     * @return SelectMainPart
     */
    public function from($table): SelectMainPart
    {
        $this->sql .= ' FROM '.$this->getQuotedTableNameDefinition($table);
        return $this;
    }

    /**
     * @param string|Table $table Either the fully qualified name or the instance of a class implementing the table interface
     * @return SelectOnPart
     */
    public function innerJoin($table): SelectOnPart
    {
        $this->sql .= ' INNER JOIN '.$this->getQuotedTableNameDefinition($table);
        return $this;
    }

    public function leftJoin($table): SelectOnPart
    {
        $this->sql .= ' LEFT JOIN '.$this->getQuotedTableNameDefinition($table);
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

    public function fetchOne() : ?string
    {
        $result = Database()->selectOne($this->toSql());
        if($result === false) {
            return null;
        }
        return (string)$result;
    }

    public function fetch(): ?Result
    {
        $row = Database()->selectRow($this->sql);
        if($row === false) {
            return null;
        }
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

    public function toSql(): string
    {
        return $this->sql;
    }
}