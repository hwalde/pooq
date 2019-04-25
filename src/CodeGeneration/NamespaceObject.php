<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\CodeGeneration;

class NamespaceObject
{
    /** @var string */
    private $name;

    /** @var string */
    private $folderPath;

    public function __construct(string $name, string $folderPath)
    {
        $this->name = $name;
        $this->folderPath = $folderPath;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFolderPath(): string
    {
        return $this->folderPath;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setFolderPath(string $folderPath): void
    {
        $this->folderPath = $folderPath;
    }
}