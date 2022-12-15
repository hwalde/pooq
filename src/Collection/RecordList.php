<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\Collection;

abstract class RecordList extends AbstractList
{
    /** Convert to an associative array */
    public function toAssoc(): array
    {
        $entries = [];
        foreach($this as $record) {
            $entries[] = $record->toAssoc();
        }
        return $entries;
    }
}