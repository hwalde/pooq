<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

interface Condition
{
    public function and(Condition $condition): Condition;

    public function or(Condition $condition): Condition;

    public function toSql(): string;
}