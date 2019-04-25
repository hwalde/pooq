<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

class OrderImpl implements Order
{
    /** @var string */
    private $sql;

    public function __construct(string $sql)
    {
        $this->sql = $sql;
    }

    public function toSql(): string
    {
        return $this->sql;
    }
}