<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\SqlBuilding\Select;

use POOQ\Field;

class SelectField
{
    /** @var Field */
    private $field;

    /** @var string */
    private $sql;

    /** @var string */
    private $aliasName;

    /**
     * @param $field Field
     * @param $sql string
     * @param $aliasName string
     */
    public function __construct($field, string $sql, string $aliasName)
    {
        $this->field = $field;
        $this->sql = $sql;
        $this->aliasName = $aliasName;
    }

    /**
     * @return Field
     */
    public function getField()
    {
        return $this->field;
    }

    public function getSql(): string
    {
        return $this->sql;
    }

    public function getAliasName(): string
    {
        return $this->aliasName;
    }
}