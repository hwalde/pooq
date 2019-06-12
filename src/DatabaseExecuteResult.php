<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

class DatabaseExecuteResult
{
    /** @var int */
    private $affectedRowsCount;

    /** @var string */
    private $lastInsertId;

    public function getAffectedRowsCount(): int
    {
        return $this->affectedRowsCount;
    }

    public function setAffectedRowsCount(int $affectedRowsCount): void
    {
        $this->affectedRowsCount = $affectedRowsCount;
    }

    public function getLastInsertId(): string
    {
        return $this->lastInsertId;
    }

    public function setLastInsertId(string $lastInsertId): void
    {
        $this->lastInsertId = $lastInsertId;
    }
}