<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\CodeGeneration;

use Database2Code\Output\Output;
use Database2Code\Output\OutputConfig;
use Database2Code\Output\PHPFile\PHPFileGenerator;

class Database2CodeOutput implements Output{
    /** @var OutputConfig */
    private $outputConfig;

    /** @var CodeGeneratorConfig */
    private $codeGeneratorConfig;

    /** @var PHPFileGenerator */
    private $modelGenerator;

    /** @var PHPFileGenerator */
    private $aliasGenerator;

    /** @var PHPFileGenerator */
    private $recordGenerator;

    /** @var PHPFileGenerator */
    private $repositoryGenerator;

    /** @var PHPFileGenerator */
    private $listGenerator;

    public function __construct(string $templateFolderPath, OutputConfig $outputConfig)
    {
        $this->outputConfig = $outputConfig;
        $this->codeGeneratorConfig = CodeGenerator::$currentConfig;

        $modelTemplateFilePath = $templateFolderPath.DIRECTORY_SEPARATOR.'model-template.php';
        $this->modelGenerator = new PHPFileGenerator($modelTemplateFilePath);

        $aliasTemplateFilePath = $templateFolderPath.DIRECTORY_SEPARATOR.'alias-template.php';
        $this->aliasGenerator = new PHPFileGenerator($aliasTemplateFilePath);

        $recordTemplateFilePath = $templateFolderPath.DIRECTORY_SEPARATOR.'record-template.php';
        $this->recordGenerator = new PHPFileGenerator($recordTemplateFilePath);

        $repositoryModelFilePath = $templateFolderPath.DIRECTORY_SEPARATOR.'repository-template.php';
        $this->repositoryGenerator = new PHPFileGenerator($repositoryModelFilePath);

        $listModelFilePath = $templateFolderPath.DIRECTORY_SEPARATOR.'list-template.php';
        $this->listGenerator = new PHPFileGenerator($listModelFilePath);
    }

    public function saveTable(\Database2Code\Struct\Table $table, string $targetDirectoryPath) {
        $entityName = $this->generateEntityName($table->getName());

        $this->generateFile($this->modelGenerator, $table, $targetDirectoryPath, $entityName);
        $this->generateFile($this->aliasGenerator, $table, $targetDirectoryPath, $entityName.'Alias');
        $this->generateFile($this->recordGenerator, $table, $targetDirectoryPath, 'Generated'.$entityName.'Record');
        $this->generateFile($this->repositoryGenerator, $table, $targetDirectoryPath, 'Generated'.$entityName."Repository");
        $this->generateFile($this->listGenerator, $table, $targetDirectoryPath, 'Generated'.$entityName."RecordList");
        $this->generateOneTimeGeneratedFiles($entityName);
    }

    protected function generateEntityName(string $tablename) : string
    {
        $tablename = $this->codeGeneratorConfig->getNameMap()[$tablename] ?? $tablename;
        return strtoupper($tablename[0]) . substr($tablename, 1);
    }

    protected function generateFile(PHPFileGenerator $fileGenerator,
                                    \Database2Code\Struct\Table $table,
                                    string $targetDirectoryPath,
                                    string $fileName): void
    {
        $fileContent = $fileGenerator->generateFromTable($table, $this->outputConfig);
        $filePath = $targetDirectoryPath.DIRECTORY_SEPARATOR.$fileName.'.php';
        $this->saveFile($filePath, $fileContent);
    }

    protected function saveFile(string $filePath, string $fileContents): void
    {
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        file_put_contents($filePath, $fileContents);
    }

    private function generateOneTimeGeneratedFiles(string $entityName)
    {
        $namespace = $this->getNamespace($entityName);

        $this->generateOneTimeGeneratedFileIfNotExists($entityName.'Record', $namespace);
        $this->generateOneTimeGeneratedFileIfNotExists($entityName.'RecordList', $namespace);
        $this->generateOneTimeGeneratedFileIfNotExists($entityName.'Repository', $namespace);
    }

    private function getNamespace(string $entityName) : NamespaceObject
    {
        if (isset($this->codeGeneratorConfig->getModelName2NamespaceMap()[$entityName])) {
            return $this->codeGeneratorConfig->getModelName2NamespaceMap()[$entityName];
        } else {
            return new NamespaceObject('generated', $this->codeGeneratorConfig->getGensrcFolderPath());
        }
    }

    private function generateOneTimeGeneratedFileIfNotExists(string $name, NamespaceObject $namespace): void
    {
        if(!is_dir($namespace->getFolderPath())) {
            mkdir($namespace->getFolderPath(), 0777, true);
        }
        $filePath = $namespace->getFolderPath() . DIRECTORY_SEPARATOR . $name . '.php';
        if (!file_exists($filePath)) {
            $sourceCode = $this->getOneTimeGeneratedFileContent($namespace->getName(), $name);
            $this->saveFile($filePath, $sourceCode);
        }
    }

    private function getOneTimeGeneratedFileContent(string $namespace, string $name): string
    {
        $isGeneratedNamespace = $namespace=='generated';
        $useStatement = $isGeneratedNamespace ? '' : "use generated\Generated{$name};\n";
        If($isGeneratedNamespace) {
            $message = "@todo: Set target-location of this file using CodeGeneratorConfig->setModelName2NamespaceMap()! Meanwhile do not(!) place custom functionality yet, because it will be overwritten!";
        } else {
            $message = "Place custom functionality here";
        }
        $copyright = CodeGenerator::$currentConfig->getCopyrightInformation();
        return <<<END
<?php
$copyright
namespace {$namespace};
 
$useStatement 
class {$name} extends Generated{$name} {

    // $message

}
END;
    }
}