<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-31
 * Time: 21:05
 */

namespace Aigisu\Data\Tables;


use Illuminate\Database\Schema\Blueprint;

class Settings implements Table
{
    /**
     * @return string
     */
    public function getTableName() : string
    {
        return 'settings';
    }

    /**
     * @param Blueprint $table
     * @return void
     */
    public function onUpdate(Blueprint $table)
    {
        //here make all changes
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
        $table->string('name', 64);
        $table->text('value');
    }
}
