<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

use POOQ\SqlBuilding\Delete\DeleteQueryBuilder;
use POOQ\SqlBuilding\Delete\DeleteWherePart;
use POOQ\SqlBuilding\Insert\InsertQueryBuilder;
use POOQ\SqlBuilding\Insert\InsertSetPart;
use POOQ\SqlBuilding\Select\SelectFromPart;
use POOQ\SqlBuilding\Select\SelectQueryBuilder;
use POOQ\SqlBuilding\Update\UpdateQueryBuilder;
use POOQ\SqlBuilding\Update\UpdateSetPart;

function &Database(): Database
{
    static $instance = null;
    if ($instance == null) {
        $instance = new Database(POOQ::getPdo());
    }
    return $instance;
}

/**
 * @param FieldOrTable[]|string ...$fieldOrTableList Can be an instance of FieldOrTable or a fully qualified name to such class
 * @return SelectFromPart
 */
function select(...$fieldOrTableList) : SelectFromPart
{
    $qb = new SelectQueryBuilder();
    return call_user_func_array([$qb, 'select'], $fieldOrTableList);
}

/**
 * Create an "SELECT COUNT(*) FROM ..." query
 * @return SelectFromPart
 */
function selectCount() : SelectFromPart
{
    $qb = new SelectQueryBuilder();
    return $qb->selectCount();
}

/**
 * @param string|Table|TableAlias $table Either the fully qualified name or the instance of a class implementing the table interface
 * @return UpdateSetPart
 */
function update($table) : UpdateSetPart
{
    return (new UpdateQueryBuilder())->update($table);
}

function value($value): Value
{
    return new Value($value);
}

/**
 * @param string|Table|TableAlias $table Either the fully qualified name or the instance of a class implementing the table interface
 * @return InsertSetPart
 */
function insertInto($table) : InsertSetPart
{
    return (new InsertQueryBuilder())->insertInto($table);
}

/**
 * @param string|Table|TableAlias $table Either the fully qualified name or the instance of a class implementing the table interface
 * @return DeleteWherePart
 */
function delete($table) : DeleteWherePart
{
    return (new DeleteQueryBuilder())->delete($table);
}