<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

abstract class FieldOrValue
{
    public function eq(FieldOrValue $fieldOrValue) : Condition
    {
        return new SimpleCondition($this->toSql().' = '.$fieldOrValue->toSql());
    }

    public function bitAnd(FieldOrValue $fieldOrValue) : Condition
    {
        return new SimpleCondition($this->toSql().' & '.$fieldOrValue->toSql());
    }

    public function in(array $values) : Condition
    {
        $quotedValuesList = [];
        foreach ($values as $value) {
            $quotedValuesList[] = Database()->quote($value);
        }
        $inClause = '('.implode(', ', $quotedValuesList).')';
        return new SimpleCondition($this->toSql().' IN '.$inClause);
    }

    public function isNull() : Condition
    {
        return new SimpleCondition($this->toSql().' IS NULL');
    }

    public abstract function toSql(): string;
}