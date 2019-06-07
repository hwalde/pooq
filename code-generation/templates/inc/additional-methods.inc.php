<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */

$columnsList = [];
$nullableColumnsList = [];
$column2TypeMap = [];
$column2NameMap = [];

/** @var $table \Database2Code\Struct\Table */
foreach ($table->getColumns() as $column) {
    $columnsList[] = '\''.$column->getName().'\'';
    if($column->isNullable()) {
        $nullableColumnsList[] = '\''.$column->getName().'\'';
    }
    $column2TypeMap[] = "\n\t\t\t".'\''.$column->getName().'\' => \''.$column->getType()->getPseudoName().'\'';
    $name = $nameMap[$column->getName()] ?? $column->getName();
    $column2NameMap[] = "\n\t\t\t".'\''.$column->getName().'\' => \''.$name.'\'';
}

$columnsListAsPHP = '['.implode(', ', $columnsList).']';
$nullableColumnsListAsPHP = '['.implode(', ', $nullableColumnsList).']';
$column2TypeMapAsPHP = '['.implode(', ', $column2TypeMap)."\n\t\t]";
$column2NameMapAsPHP = '['.implode(', ', $column2NameMap)."\n\t\t]";

$quotedPrimaryKeyColumnList = [];
foreach ($table->getPrimaryKeyColumnList() as $primaryKeyColumn) {
    $quotedPrimaryKeyColumnList[] = '\''.$primaryKeyColumn->getName().'\'';
}
$primaryKeyColumnsAsString = implode(', ', $quotedPrimaryKeyColumnList);

return <<<ENDER
    public function getTableName(): string
    {
        return '{$table->getName()}';
    }
    
    public function __listColumns() : array
    {
        return {$columnsListAsPHP};
    }
    
    /**
     * @return string[]
     */
    public function __listPrimaryKeyColumns(): array
    {
        return [$primaryKeyColumnsAsString];
    }
    
    public function __listNullableColumns() : array
    {
        return {$nullableColumnsListAsPHP};
    }
    
    public function __getColumn2TypeMap() : array
    {
        return {$column2TypeMapAsPHP};
    }
    
    public function __getColumn2NameMap() : array
    {
        return {$column2NameMapAsPHP};
    }
ENDER;
