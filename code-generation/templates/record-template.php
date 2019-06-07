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

include_once __DIR__.'/inc/get-mapped-name-function.inc.php';

if(!function_exists('PHPFile__record__getColumnProperty')) {
    function PHPFile__record__getColumnProperty(\Database2Code\Struct\Column $column, array $nameMap)
    {
        $name = PHPFile__POOQ__getMappedName($column, $nameMap);
        if($column->getType() instanceof \Database2Code\Struct\ColumnType\UnknownColumnType) {
            $phpType = 'mixed';
        } else {
            $phpType = $column->getType()->getPHPTypeName();
        }
        if($column->isNullable()) {
            $phpType .= '|null';
        }
        return <<<END
    
    /** @var \${$name} $phpType */
    protected \${$name};
END;
    }
}

if(!function_exists('PHPFile__record__getColumnMethods')) {
    function PHPFile__record__getColumnMethods(\Database2Code\Struct\Column $column, \Database2Code\Output\OutputConfig $config, array $nameMap)
    {
        $name = PHPFile__POOQ__getMappedName($column, $nameMap);
        if($column->getType() instanceof \Database2Code\Struct\ColumnType\UnknownColumnType) {
            $phpType = 'mixed';
            if($column->isNullable()) {
                $phpType .= '|null';
            }
            $returnTypeDef = '';
            $argumentTypeDef = '';
        } else {
            $phpType = $column->getType()->getPHPTypeName();

            if($column->isNullable()) {
                if(version_compare($config->getPhpVersion(), '7.1', '>=')) {
                    $returnTypeDef = ' : ?'.$phpType;
                    $argumentTypeDef = '?'.$phpType.' ';
                } else {
                    $returnTypeDef = '';
                    $argumentTypeDef = '';
                }
                $phpType .= '|null';
            } else {
                $returnTypeDef = ' : '.$phpType;
                $argumentTypeDef = $phpType.' ';
            }
        }
        $setterPHPDoc = <<<END

    /**
     * @param $phpType \${$name}
     */
END;
        $getterPHPDoc = <<<END

    /**
     * @return $phpType
     */
END;
        $upperCaseName = strtoupper($name[0]) . substr($name, 1);
        return <<<END
    
    public function has{$upperCaseName}(): bool
    {
        return isset(\$this->$name);
    }    
$getterPHPDoc
    public function get{$upperCaseName}()$returnTypeDef
    {
        return \$this->$name;
    }
$setterPHPDoc
    public function set{$upperCaseName}($argumentTypeDef\$$name)
    {
        \$this->$name = \$$name;
    }
END;
    }
}

$properties = '';
foreach ($table->getColumns() as $column) {
    $properties .= PHPFile__record__getColumnProperty($column, $nameMap).PHP_EOL;
}

$methods = '';
foreach ($table->getColumns() as $column) {
    $methods .= PHPFile__record__getColumnMethods($column, $config, $nameMap).PHP_EOL;
}

if($config->hasNamespace()) {
    $namespace = "namespace {$config->getNamespace()};\n";
} else {
    $namespace = '';
}

$name = $nameMap[$table->getName()] ?? $table->getName();
$name = strtoupper($name[0]).substr($name, 1);

$targetNamespaceMap = CodeGenerator::$currentConfig->getModelName2NamespaceMap();
$useStatements = '';
if(isset($targetNamespaceMap[$name])) {
    $targetNamespace = $targetNamespaceMap[$name]->getName();
    /*$useStatements .= "use ".$targetNamespace."\\$name;\n";
    $useStatements .= "use ".$targetNamespace."\\".$name."List;\n";
    $useStatements .= "use ".$targetNamespace."\\".$name."Repository;\n";*/
}

$constants = '';
$copyright = CodeGenerator::$currentConfig->getCopyrightInformation();

if($table->containsPrimaryKey()) {
    $extends = ' extends AbstractUpdateableRecord';
    $implements = ' implements UpdateableRecord';

    // __listPrimaryKeyColumns-method:
    $quotedPrimaryKeyColumnList = [];
    foreach ($table->getPrimaryKeyColumnList() as $primaryKeyColumn) {
        $quotedPrimaryKeyColumnList[] = '\''.$primaryKeyColumn->getName().'\'';
    }
    $primaryKeyColumnsAsString = implode(', ', $quotedPrimaryKeyColumnList);
    $methods .= <<<END
    /**
     * @return string[]
     */
    protected function __listPrimaryKeyColumns(): array;
    {
        return [$primaryKeyColumnsAsString];
    }
END;

} else {
    $extends = '';
    $implements = ' implements Record';
}

return <<<END
<?php
$copyright
$namespace
use POOQ\Record;
$useStatements
class Generated{$name}Record$extends$implements {
$constants$properties$methods
    
    /** @noinspection PhpHierarchyChecksInspection */
    /** @noinspection PhpSignatureMismatchDuringInheritanceInspection */
    public function __getModel(): {$name}
    {
        return new {$name}();
    }
}
END;
