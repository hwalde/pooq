<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

class ColumnField extends AbstractColumnField
{
    public function as($aliasName): AliasedColumnField
    {
        return new AliasedColumnField($this->getTableName(), $this->getColumnName(), $aliasName, $this->getTableAliasName());
    }

    public function getFieldName(): string
    {
        return $this->getColumnName();
    }

    public function toSql(): string
    {
        if($this->hasTableAliasName()) {
            $tableName = $this->getTableAliasName();
        } else {
            $tableName = $this->getTableName();
        }
        return Database()->quoteIdentifier($tableName).
            '.'.Database()->quoteIdentifier($this->getColumnName());
    }
}