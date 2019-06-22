<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\SqlBuilding\Select;

use POOQ\Result;
use POOQ\ResultList;

interface SelectEndPart
{
    public function fetch() : Result;

    public function fetchOne() : string;

    /**
     * @return ResultList|Result[]
     */
    public function fetchAll() : ResultList;

    public function toSql() : string;
}