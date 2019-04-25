<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

class AliasedColumnField extends AbstractColumnField implements FieldOrTable
{
    /** @var string */
    private $columnAliasName;

    public function __construct(string $tableName, string $columnName, string $columnAliasName, ?string $tableAliasName = null)
    {
        parent::__construct($tableName, $columnName, $tableAliasName);
        $this->columnAliasName = $columnAliasName;
    }

    public function getColumnAliasName(): string
    {
        return $this->columnAliasName;
    }

    public function getFieldName(): string
    {
        return $this->getColumnAliasName();
    }

    public function toSql(): string
    {
        if($this->hasTableAliasName()) {
            $tableName = $this->getTableAliasName();
        } else {
            $tableName = $this->getTableName();
        }
        return Database()->quoteIdentifier($tableName).
            '.'.Database()->quoteIdentifier($this->getColumnName()).
            ' AS '.Database()->quoteIdentifier($this->getColumnAliasName());
    }
}