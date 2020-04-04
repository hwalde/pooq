<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\SqlBuilding;

use POOQ\FieldOrTable;
use POOQ\Table;
use POOQ\TableAlias;
use function POOQ\Database;

trait SqlBuildingHelperTrait
{
    /**
     * @param string|Table|TableAlias $table Either the fully qualified name or the instance of a class implementing the table interface
     */
    private function getQuotedTableNameDefinition($table): string
    {
        $table = $this->generateTableObject($table);

        $quotedTableName = Database()->quoteIdentifier($table->getTableName());

        if ($table instanceof TableAlias) {
            if($table->getAliasName() == $table->getTableName()) {
                return $quotedTableName;
            }
            return $quotedTableName . ' ' . Database()->quoteIdentifier($table->getAliasName());
        } else {
            return $quotedTableName;
        }
    }

    /**
     * @param string|Table|TableAlias $table Either the fully qualified name or the instance of a class implementing the table interface
     * @return Table
     */
    private function generateTableObject($table)
    {
        if (is_string($table)) {
            $table = new $table();
        }
        if ($table instanceof Table || $table instanceof TableAlias) {
            return $table;
        } else {
            throw new \InvalidArgumentException('Expected instance of "' . Table::class . '" or "' . TableAlias::class . '"!');
        }
    }

    private function getQuotedTableName($table): string
    {
        $table = $this->generateTableObject($table);

        if ($table instanceof TableAlias) {
            return Database()->quoteIdentifier($table->getAliasName());
        } else {
            return Database()->quoteIdentifier($table->getTableName());;
        }
    }
}