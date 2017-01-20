<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-31
 * Time: 21:06
 */

namespace Aigisu\Api\Data\Tables;


use Illuminate\Database\Schema\Blueprint;

interface Table
{
    /**
     * @return string
     */
    public function getTableName() : string;

    /**
     * @param Blueprint $table
     * @return void
     */
    public function onCreate(Blueprint $table);
}
