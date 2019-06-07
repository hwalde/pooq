<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */

use POOQ\CodeGeneration\CodeGenerator;

/** @var $table \Database2Code\Struct\Table */
/** @var $config \Database2Code\Output\OutputConfig */

$nameMap = CodeGenerator::$currentConfig->getNameMap();

$modelName = $nameMap[$table->getName()] ?? $table->getName();
$modelName = strtoupper($modelName[0]).substr($modelName, 1);

include_once __DIR__.'/inc/get-mapped-name-function.inc.php';

if(!function_exists('PHPFile__POOQ__aliasMethods')) {
    function PHPFile__POOQ__aliasMethods(string $modelName, string $tableName, \Database2Code\Struct\Column $column, \Database2Code\Output\OutputConfig $config, array $nameMap)
    {
        $name = PHPFile__POOQ__getMappedName($column, $nameMap);
        return <<<END
    public function {$name}() : ColumnField
    {
        return new ColumnField('$name', '$modelName', '$tableName', '{$column->getName()}', \$this->getAliasName());
    }
END;
    }
}

$functionCallList = [];
$methods = '';
foreach ($table->getColumns() as $column) {
    $methods .= PHPFile__POOQ__aliasMethods($modelName, $table->getName(), $column, $config, $nameMap).PHP_EOL;
    $functionCallList[] = "\$this->".PHPFile__POOQ__getMappedName($column, $nameMap)."()";
}
$functionCalls = implode(",\n\t\t\t", $functionCallList);

if($config->hasNamespace()) {
    $namespace = "namespace {$config->getNamespace()};\n";
} else {
    $namespace = '';
}

$additionalMethods = include __DIR__.'/inc/additional-methods.inc.php';


$targetNamespaceMap = CodeGenerator::$currentConfig->getModelName2NamespaceMap();
$useStatements = '';
if(isset($targetNamespaceMap[$modelName])) {
    $targetNamespace = $targetNamespaceMap[$modelName]->getName();
    $useStatements .= "use ".$targetNamespace."\\".$modelName."Record;\n";
    /*$useStatements .= "use ".$targetNamespace."\\".$name."List;\n";
    $useStatements .= "use ".$targetNamespace."\\".$name."Repository;\n";*/
}

$copyright = CodeGenerator::$currentConfig->getCopyrightInformation();

return <<<END
<?php
$copyright
$namespace
use POOQ\TableAlias;
use POOQ\ColumnField;
use POOQ\ColumnFieldList;
$useStatements
class {$modelName}Alias extends TableAlias {
   
$methods
$additionalMethods

    /**
     * @return ColumnField[]
     */
    public function __getFieldList() : ColumnFieldList
    {
        return new ColumnFieldList([
            $functionCalls
        ]);
    }
    
    /** @noinspection PhpHierarchyChecksInspection */
    /** @noinspection PhpSignatureMismatchDuringInheritanceInspection */
    public function __getRecordClass() : {$modelName}Record
    {
        return new {$modelName}Record();
    }
}
END;
