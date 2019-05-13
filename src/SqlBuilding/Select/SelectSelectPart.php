<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\SqlBuilding\Select;

use POOQ\FieldOrTable;

interface SelectSelectPart
{
    /**
     * @param FieldOrTable[]|string ...$fieldOrTableList Can be an instance of FieldOrTable or a fully qualified name to such class
     * @return SelectFromPart
     */
    public function select(...$fieldList) : SelectFromPart;

    /**
     * Create an "SELECT COUNT(*) FROM ..." query
     * @return SelectFromPart
     */
    function selectCount() : SelectFromPart;
}