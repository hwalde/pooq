<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

interface Record
{
    /**
     * @return Table
     */
    public function __getModel();

    /**
     * Returns an assoc array (name => value)
     * @return array
     */
    public function toAssoc(): array;
}