<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

class Value extends FieldOrValue
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function toSql(): string
    {
        return Database()->quote($this->value);
    }
}