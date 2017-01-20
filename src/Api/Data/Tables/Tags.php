<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-31
 * Time: 21:05
 */

namespace Aigisu\Api\Data\Tables;


use Aigisu\Api\Models\Unit\Tag;
use Illuminate\Database\Schema\Blueprint;

class Tags implements Table
{
    /**
     * @return string
     */
    public function getTableName() : string
    {
        return (new Tag())->getTable();
    }

    /**
     * @param Blueprint $table
     * @return void
     */
    public function onCreate(Blueprint $table)
    {
        $table->collation = 'utf8mb4_unicode_ci';
        $table->charset = 'utf8mb4';
        $table->engine = 'InnoDB';

        $table->increments('id')->unsigned();
        $table->string('name', 25)->unique();
        $table->timestamps();
    }
}
