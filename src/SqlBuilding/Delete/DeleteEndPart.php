<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\SqlBuilding\Delete;

interface DeleteEndPart
{
    public function toSql() : string;

    /**
     * @return int the number of deleted rows
     */
    public function execute() : int;
}