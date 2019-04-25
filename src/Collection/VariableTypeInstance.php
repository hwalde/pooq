<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\Collection;

class VariableTypeInstance implements VariableType
{
    private $fullyQualifiedClassName;

    public function __construct(string $fullyQualifiedClassName)
    {
        if(!class_exists($fullyQualifiedClassName)) {
            throw new \Error('Class "'.$fullyQualifiedClassName.'" does not exist!');
        }
        $this->fullyQualifiedClassName = $fullyQualifiedClassName;
    }

    public function isTypeOfValueValid($value): bool
    {
        return $value instanceof $this->fullyQualifiedClassName;
    }

    public function getName(): string
    {
        return $this->fullyQualifiedClassName;
    }
}