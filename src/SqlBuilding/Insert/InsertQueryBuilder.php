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
use function POOQ\Database;
use POOQ\DatabaseExecuteResult;
use POOQ\SqlBuilding\SqlBuildingHelperTrait;
use POOQ\Table;
use POOQ\TableAlias;

class InsertQueryBuilder implements InsertSetPart, InsertAfterSetPart
{
    use SqlBuildingHelperTrait;

    /** @var int */
    private $index;

    /** @var FieldValuePairList[] */
    private $rows;

    /** @var Table */
    private $table;

    /**
     * @param string|Table|TableAlias $table Either the fully qualified name or the instance of a class implementing the table interface
     * @return InsertSetPart
     */
    public function insertInto($table) : InsertSetPart
    {
        $this->rows = [];
        $this->index = 0;
        $this->rows[$this->index] = new FieldValuePairList();
        $this->table = $this->generateTableObject($table);
        return $this;
    }

    public function set(AbstractColumnField $field, $value): InsertAfterSetPart
    {
        // More precise would be comparisem of modelMame but we want to leave the possibility of creating pseudo models
        if($field->getTableName() != $this->table->getTableName()) {
            throw new \InvalidArgumentException('Expected field "'.$field->getFieldName().'"" to belong to '.get_class($this->table));
        }

        $this->rows[$this->index][] = new FieldValuePair($field, $value);
        return $this;
    }

    public function newRecord() : InsertSetPart
    {
        if($this->index>0) {
            $this->validateRecord($this->index);
        }
        $this->index++;
        $this->rows[$this->index] = [];
        return $this;
    }

    private function validateRecord(int $index)
    {
        if(count($this->rows[$index]) !== count($this->rows[0])) {
            throw new \InvalidArgumentException('Number of columns differentiate between records! Check your set()-method calls!');
        }
        /** @var FieldValuePair $pair */
        foreach ($this->rows[0] as $pair) {
            if(!$this->rows[$index]->containsField($pair->getField())) {
                throw new \InvalidArgumentException('Fields differentiate between records! Record '.($index+1).' does not contain Field "'.$pair->getField()->getFieldName().'"! Check your set()-method calls!');
            }
        }
    }

    public function toSql(): string
    {
        if ($this->index > 0) {
            $this->validateRecord($this->index);
        }

        if (count($this->rows) == 0) {
            throw new \InvalidArgumentException('Cannot execute empty insert-query! Add at least one set(..)-method call!');
        }

        $sql = 'INSERT INTO ' . $this->getQuotedTableNameDefinition($this->table);

        $quotedColumnList = [];

        /** @var FieldValuePair $fieldValuePair */
        foreach ($this->rows[0] as $fieldValuePair) {
            $quotedColumnList[] = Database()->quoteIdentifier($fieldValuePair->getField()->getColumnName());
        }

        $sql .= ' (' . implode(', ', $quotedColumnList) . ') VALUES ';

        $sqlRows = [];
        foreach ($this->rows as $list) {
            $quotedValueList = [];
            foreach ($this->rows[0] as $fieldValuePair) {
                $value = $list->getByField($fieldValuePair->getField())->getValue();
                if ($value === null) {
                    $quotedValueList[] = 'NULL';
                } else if ($value instanceof \DateTime) {
                    $quotedValueList[] = Database()->quote($value->format('Y-m-d H:i:s'));
                } else {
                    $quotedValueList[] = Database()->quote($value);
                }
            }
            $sqlRows[] = '(' . implode(', ', $quotedValueList) . ')';
        }

        return $sql.implode(', ', $sqlRows).';';
    }

    public function execute(): DatabaseExecuteResult
    {
        return Database()->execute($this->toSql());
    }
}