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

trait SqlBuildingHelperTrait
{
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
}