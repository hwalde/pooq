<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

use POOQ\Collection\AbstractList;
use POOQ\Collection\VariableType;
use POOQ\Collection\VariableTypeInstance;

class ResultList extends AbstractList
{
    function getListElementType() : VariableType
    {
        return new VariableTypeInstance(Result::class);
    }

    /**
     * @param string|Table|TableAlias $table Either the fully qualified name or the instance of a class implementing the table interface
     * @return mixed
     */
    public function into($table) {
        if(is_string($table)) {
            $table = new $table();
        }
        if(!$table instanceof Table) {
            throw new \InvalidArgumentException('Expecting $table to be of type Table!');
        }

        $recordClassName = get_class($table->__getRecordClass());
        $recordListClassName = $recordClassName.'List';

        $list = new $recordListClassName();
        /** @var Result $result */
        foreach ($this as $result) {
            $list[] = $result->into($table);
        }
        return $list;
    }
}