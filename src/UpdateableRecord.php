<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

interface UpdateableRecord extends Record
{
    /**
     * Reload the record from the database
     */
    public function refresh(): void;

    /**
     * insert or update the record to the database
     */
    public function store(): ?int;

    public function delete(): int;
}