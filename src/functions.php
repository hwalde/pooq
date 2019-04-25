<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

use POOQ\SqlBuilding\Select\SelectFromPart;
use POOQ\SqlBuilding\Select\SelectQueryBuilder;

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

function value($value) {
    return new Value($value);
}


