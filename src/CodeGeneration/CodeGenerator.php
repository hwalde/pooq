<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\CodeGeneration;

use Database2Code\Input\MySQL\MySQLInputConfig;
use Database2Code\Output\OutputConfig;
use Database2Code\Service\ConvertService;

class CodeGenerator
{
    /**
     * This public static is a temporal hack until Database2Code is improved
     * @deprecated
     * @var CodeGeneratorConfig
     */
    public static $currentConfig;

    public function __construct(CodeGeneratorConfig $config)
    {
        self::$currentConfig = $config;
    }

    public function convertDatabase(string $databaseName, string $databaseUsername, string $databasePassword,
                                    string $databaseHostname, int $databasePort = 3306) {
        $genDirFolderPath = self::$currentConfig->getGensrcFolderPath();
        $this->validateGensrcFolderPath($genDirFolderPath);

        $this->emptyGensrcFolder($genDirFolderPath);

        $outputConfig = new OutputConfig();
        $outputConfig->setNamespace("generated");
        $outputConfig->setPhpVersion("7.3");
        $templatesFolder = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.
            DIRECTORY_SEPARATOR.'code-generation'.DIRECTORY_SEPARATOR.'templates';
        $service = new ConvertService(new Database2CodeOutput($templatesFolder, $outputConfig));

        $inputConfig = new MySQLInputConfig($databaseUsername, $databasePassword, $databaseHostname, $databasePort);
        $service->convertDatabase($inputConfig, $databaseName, $genDirFolderPath);
    }

    private function validateGensrcFolderPath(string $gensrcFolderPath): void
    {
        if (!is_dir($gensrcFolderPath)) {
            throw new \InvalidArgumentException('$gensrcFolderPath must be a valid, readable path to a folder!');
        }
        if (!is_readable($gensrcFolderPath)) {
            throw new \InvalidArgumentException('$gensrcFolderPath folder is not writeable!');
        }
        if (!is_writable($gensrcFolderPath)) {
            throw new \InvalidArgumentException('$gensrcFolderPath folder is not writeable!');
        }
    }

    private function emptyGensrcFolder(string $genDirFolderPath): void
    {
        foreach (scandir($genDirFolderPath) as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $filePath = $genDirFolderPath . DIRECTORY_SEPARATOR . $file;
            unlink($filePath) or die("Couldn't delete file \"$filePath\"!");
        }
    }
}