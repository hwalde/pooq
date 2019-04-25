<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

interface Table extends FieldOrTable
{
    public function getTableName() : string;

    /**
     * @return ColumnField[]
     */
    public function __getFieldList() : ColumnFieldList;

    /**
     * @return Record
     */
    public function __getRecordClass();

    public function __listColumns() : array;

    public function __listNullableColumns() : array;

    public function __getColumn2TypeMap() : array;

    public function __getColumn2NameMap() : array;
}