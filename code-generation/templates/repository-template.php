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
$targetNamespaceMap = CodeGenerator::$currentConfig->getModelName2NamespaceMap();

$name = $nameMap[$table->getName()] ?? $table->getName();
$name = strtoupper($name[0]).substr($name, 1);

$namespace = $config->getNamespace();
$useStatements = '';
if(isset($targetNamespaceMap[$name])) {
    $targetNamespace = $targetNamespaceMap[$name]->getName();
    $useStatements .= "use ".$targetNamespace."\\".$name."Record;\n";
    $useStatements .= "use ".$targetNamespace."\\".$name."RecordList;\n";
}

$copyright = CodeGenerator::$currentConfig->getCopyrightInformation();

return <<<END
<?php
$copyright
namespace $namespace;

use POOQ\Repository;
$useStatements
class Generated{$name}Repository extends Repository {

    // Not used atm

}
END;
