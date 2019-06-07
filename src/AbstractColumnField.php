<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

abstract class AbstractColumnField extends Field implements FieldOrTable
{
    /** @var string */
    private $fieldName;

    /** @var string */
    private $modelName;

    /** @var string */
    private $tableName;

    /** @var string|null */
    private $tableAliasName;

    /** @var string */
    private $columnName;

    public function __construct(string $fieldName, string $modelName, string $tableName,
                                string $columnName, ?string $tableAliasName = null)
    {
        $this->fieldName = $fieldName;
        $this->modelName = $modelName;
        $this->tableName = $tableName;
        $this->tableAliasName = $tableAliasName;
        $this->columnName = $columnName;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getModelName(): string
    {
        return $this->modelName;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function hasTableAliasName(): bool
    {
        return isset($this->tableAliasName);
    }

    public function getTableAliasName(): ?string
    {
        return $this->tableAliasName;
    }

    public function getColumnName(): string
    {
        return $this->columnName;
    }
}