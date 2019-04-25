<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */

/**
 * @param \Database2Code\Struct\Column $column
 * @param array $nameMap
 * @return mixed|string
 */
if(!function_exists('PHPFile__POOQ__getMappedName')) {
    function PHPFile__POOQ__getMappedName(\Database2Code\Struct\Column $column, array $nameMap)
    {
        $name = $column->getName();
        if (isset($nameMap[$name])) {
            $name = $nameMap[$name];
        }
        return $name;
    }
}