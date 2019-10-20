<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\CodeGeneration;

class CodeGeneratorConfig
{
    /** @var array */
    private $nameMap = [];

    /** @var ModelName2NamespaceMap */
    private $modelName2NamespaceMap = [];

    /** @var string */
    private $gensrcFolderPath;

    /** @var string */
    private $generatedFilesNamespace = 'generated';

    /** @var string */
    private $copyrightInformation = '';

    public function __construct(string $gensrcFolderPath)
    {
        $this->gensrcFolderPath = $gensrcFolderPath;
    }

    public function getNameMap(): array
    {
        return $this->nameMap;
    }

    public function setNameMap(array $nameMap): void
    {
        $this->nameMap = $nameMap;
    }

    /**
     * @return NamespaceObject[]
     */
    public function getModelName2NamespaceMap(): ModelName2NamespaceMap
    {
        return $this->modelName2NamespaceMap;
    }

    public function setModelName2NamespaceMap(ModelName2NamespaceMap $modelName2NamespaceMap): void
    {
        $this->modelName2NamespaceMap = $modelName2NamespaceMap;
    }

    public function getGensrcFolderPath(): string
    {
        return $this->gensrcFolderPath;
    }

    public function getGeneratedFilesNamespace(): string
    {
        return $this->generatedFilesNamespace;
    }

    public function setGeneratedFilesNamespace(string $namespace): void
    {
        $this->generatedFilesNamespace = $namespace;
    }

    public function getCopyrightInformation(): string
    {
        return $this->copyrightInformation;
    }

    public function setCopyrightInformation(string $copyrightInformation): void
    {
        $this->copyrightInformation = $copyrightInformation;
    }
}
