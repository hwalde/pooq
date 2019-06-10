<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

class RecordValue
{
    /** @var mixed */
    private $value;

    /** @var mixed */
    private $originalValue;

    /** @var bool */
    private $hasBeenLoadedFromDatabase = false;

    /** @var bool */
    private $changed = false;

    public function hasBeenSet() : bool
    {
        return $this->changed || $this->hasBeenLoadedFromDatabase;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): void
    {
        $this->value = $value;
    }

    public function getOriginalValue()
    {
        return $this->originalValue;
    }

    public function setOriginalValue($originalValue): void
    {
        $this->originalValue = $originalValue;
    }

    public function hasBeenLoadedFromDatabase(): bool
    {
        return $this->hasBeenLoadedFromDatabase;
    }

    public function setHasBeenLoadedFromDatabase(bool $hasBeenLoadedFromDatabase): void
    {
        $this->hasBeenLoadedFromDatabase = $hasBeenLoadedFromDatabase;
    }

    public function isChanged(): bool
    {
        return $this->changed;
    }

    public function setChanged(bool $changed): void
    {
        $this->changed = $changed;
    }
}