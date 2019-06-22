<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\SqlBuilding\Update;

use POOQ\AbstractColumnField;
use POOQ\Condition;
use function POOQ\Database;
use POOQ\SqlBuilding\Select\SelectEndPart;
use POOQ\SqlBuilding\SqlBuildingHelperTrait;
use POOQ\Table;
use POOQ\TableAlias;

class UpdateQueryBuilder implements UpdateSetPart, UpdateWherePart, UpdateEndPart
{
    use SqlBuildingHelperTrait;

    /** @var string */
    private $sql;

    /** @var bool */
    private $hasSetBeenCalled;

    /**
     * @param string|Table|TableAlias $table Either the fully qualified name or the instance of a class implementing the table interface
     * @return UpdateSetPart
     */
    function update($table) : UpdateSetPart
    {
        $this->hasSetBeenCalled = false;
        $this->sql = 'UPDATE '.$this->getQuotedTableName($table).' SET ';
        return $this;
    }

    /**
     * @param AbstractColumnField $field
     * @param string|int|float|bool|double|SelectEndPart $value
     * @return UpdateWherePart
     */
    public function set(AbstractColumnField $field, $value): UpdateWherePart
    {
        if($this->hasSetBeenCalled) {
            $this->sql .= ', ';
        }
        $this->hasSetBeenCalled = true;

        $this->sql .= Database()->quoteIdentifier($field->getColumnName()).' = ';
        if($value === null) {
            $this->sql .= 'NULL';
        } else if ($value instanceof \DateTime) {
            $this->sql .= Database()->quote($value->format('Y-m-d H:i:s'));
        } else if($value instanceof SelectEndPart) {
            $this->sql .= '('.$value->toSql().')';
        } else {
            $this->sql .= Database()->quote($value);
        }
        return $this;
    }

    /**
     * @param Condition $condition
     * @return UpdateEndPart
     */
    public function where(Condition $condition): UpdateEndPart
    {
        $this->sql .= ' WHERE '.$condition->toSql();
        return $this;
    }

    public function toSql(): string
    {
        return $this->sql;
    }

    /**
     * @return int the number of updated records
     */
    public function execute(): int
    {
        return Database()->executeAndCountAffectedRows($this->sql);
    }
}