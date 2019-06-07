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

/*if(!function_exists('PHPFile__constants__getColumnConstant')) {
    function PHPFile__constants__getColumnConstant(\Database2Code\Struct\Column $column, array $nameMap)
    {
        $name = PHPFile__getMappedName($column, $nameMap);
        return <<<END
    const {$name} = '{$column->getName()}';
END;
    }
}
*/
$constants = '';
/*foreach ($table->getColumns() as $column) {
    $constants .= PHPFile__constants__getColumnConstant($column, $nameMap).PHP_EOL;
}*/

if(!function_exists('PHPFile__POOQ__getModelMethods')) {
    function PHPFile__POOQ__getModelMethods(string $modelName, string $tableName, \Database2Code\Struct\Column $column,
                                            \Database2Code\Output\OutputConfig $config, array $nameMap)
    {
        $name = PHPFile__POOQ__getMappedName($column, $nameMap);
        return <<<END
    public static function {$name}() : ColumnField
    {
        return new ColumnField('$name', '$modelName', '$tableName', '{$column->getName()}');
    }
END;
    }
}

$functionCallList = [];
$methods = '';
foreach ($table->getColumns() as $column) {
    $methods .= PHPFile__POOQ__getModelMethods($modelName, $table->getName(), $column, $config, $nameMap).PHP_EOL;
    $functionCallList[] = "self::".PHPFile__POOQ__getMappedName($column, $nameMap)."()";
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
use POOQ\ColumnField;
use POOQ\ColumnFieldList;
use POOQ\Table;
$useStatements
class {$modelName} implements Table {

    const table = '{$table->getName()}';
   
    public static function as(string \$aliasName): {$modelName}Alias
    {
        return new {$modelName}Alias(\$aliasName);
    }
    
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
    
$constants$methods
$additionalMethods
}
END;
