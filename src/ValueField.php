<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

class ValueField extends Field implements FieldOrTable
{
    /** @var string  */
    private $name;

    /** @var mixed */
    private $value;

    public function __construct(string $name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function getFieldName(): string
    {
        return $this->name;
    }

    public function toSql(): string
    {
        return Database()->quote($this->value).' AS '.Database()->quoteIdentifier($this->getFieldName());
    }
}