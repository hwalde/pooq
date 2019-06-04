<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\SqlBuilding\Update;

use POOQ\AbstractColumnField;
use POOQ\SqlBuilding\Select\SelectEndPart;

interface UpdateSetPart
{
    /**
     * @param AbstractColumnField $field
     * @param string|int|float|bool|double|SelectEndPart $value
     * @return UpdateWherePart
     */
    public function set(AbstractColumnField $field, $value) : UpdateWherePart;
}