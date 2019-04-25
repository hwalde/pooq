<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\Collection;

class VariableTypeInteger implements KeyableVariableType
{
    public function isTypeOfValueValid($value): bool
    {
        return is_int($value);
    }

    public function getName(): string
    {
        return 'integer';
    }
}