<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

class CustomField extends Field implements FieldOrTable
{
    /** @var string  */
    private $name;

    /** @var string */
    private $sql;

    public function __construct(string $name, string $sql)
    {
        $this->name = $name;
        $this->sql = $sql;
    }

    public function getFieldName(): string
    {
        return $this->name;
    }

    public function toSql(): string
    {
        return $this->sql.' AS '.Database()->quoteIdentifier($this->getFieldName());
    }
}