<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

class SimpleCondition implements Condition
{
    /** @var string */
    private $sql;
    
    public function __construct(string $sql)
    {
        $this->sql = $sql;
    }

    public function toSql(): string
    {
        return $this->sql;
    }

    public function and(Condition $condition): Condition
    {
        return new SimpleCondition('('.$this->toSql().' AND '.$condition->toSql().')');
    }

    public function or(Condition $condition): Condition
    {
        return new SimpleCondition('('.$this->toSql().' OR '.$condition->toSql().')');
    }
}