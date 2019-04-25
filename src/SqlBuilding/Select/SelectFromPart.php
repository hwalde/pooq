<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\SqlBuilding\Select;

use POOQ\Table;

interface SelectFromPart
{
    /**
     * @param string|Table $table Either the fully qualified name or the instance of a class implementing the table interface
     * @return SelectMainPart
     */
    public function from($table) : SelectMainPart;
}