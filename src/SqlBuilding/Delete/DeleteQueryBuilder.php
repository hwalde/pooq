<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\SqlBuilding\Delete;

use POOQ\Condition;
use function POOQ\Database;
use POOQ\SqlBuilding\SqlBuildingHelperTrait;
use POOQ\Table;
use POOQ\TableAlias;

class DeleteQueryBuilder implements DeleteWherePart, DeleteOnPart, DeleteEndPart
{
    use SqlBuildingHelperTrait;

    /** @var string */
    private $sql;

    /**
     * @param string|Table|TableAlias $table Either the fully qualified name or the instance of a class implementing the table interface
     * @return DeleteWherePart
     */
    public function delete($table): DeleteWherePart
    {
        $this->sql = 'DELETE '.$this->getQuotedTableName($table).' FROM '.$this->getQuotedTableNameDefinition($table);
        return $this;
    }

    public function innerJoin($table): DeleteOnPart
    {
        $this->sql .= ' INNER JOIN '.$this->getQuotedTableNameDefinition($table);
        return $this;
    }

    public function leftJoin($table): DeleteOnPart
    {
        $this->sql .= ' LEFT JOIN '.$this->getQuotedTableNameDefinition($table);
        return $this;
    }

    public function on(Condition $condition): DeleteWherePart
    {
        $this->sql .= ' ON '.$condition->toSql();
        return $this;
    }

    public function where(Condition $condition): DeleteEndPart
    {
        $this->sql .= ' WHERE '.$condition->toSql();
        return $this;
    }

    public function toSql(): string
    {
        return $this->sql;
    }

    /**
     * @return int the number of inserted records
     */
    public function execute(): int
    {
        return Database()->executeAndCountAffectedRows($this->sql);
    }
}