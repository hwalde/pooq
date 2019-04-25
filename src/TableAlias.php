<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

abstract class TableAlias implements Table
{
    /** @var string  */
    private $aliasName;

    public function __construct(string $aliasName)
    {
        $this->aliasName = $aliasName;
    }

    public function getAliasName(): string
    {
        return $this->aliasName;
    }

    public function setAliasName(string $aliasName): void
    {
        $this->aliasName = $aliasName;
    }
}