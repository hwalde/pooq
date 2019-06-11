<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\SqlBuilding\Insert;

interface InsertAfterSetPart extends InsertSetPart
{
    public function newRecord() : InsertSetPart;

    public function toSql() : string;

    /**
     * @return int the number of inserted rows
     */
    public function executeAndCountAffectedRows() : int;

    /**
     * @return the last insert id
     */
    public function execute() : string;
}