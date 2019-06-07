<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\Field\Functions\String;

use function POOQ\Database;
use POOQ\Field;
use POOQ\Field\Functions\FunctionField;

class LengthFunction extends FunctionField implements StringFunction
{
    /** @var Field */
    private $subject;

    public function __construct(Field $subject)
    {
        $this->subject = $subject;
    }

    public function getSqlName(): string
    {
        return 'length';
    }

    public function toSql(): string
    {
        return 'length('.Database()->quoteIdentifier($this->subject->getSqlName()).')';
    }
}