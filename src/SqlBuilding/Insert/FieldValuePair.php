<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\SqlBuilding\Insert;

use POOQ\AbstractColumnField;

class FieldValuePair
{
    /** @var AbstractColumnField */
    private $field;

    /** @var string|int|float|bool|double */
    private $value;

    public function __construct(AbstractColumnField $field, $value)
    {
        $this->field = $field;
        $this->value = $value;
    }

    public function getField(): AbstractColumnField
    {
        return $this->field;
    }

    /**
     * @return bool|float|int|mixed|string
     */
    public function getValue()
    {
        return $this->value;
    }
}