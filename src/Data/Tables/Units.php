<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-31
 * Time: 21:05
 */

namespace Aigisu\Data\Tables;


use Aigisu\Models\Unit;
use Illuminate\Database\Schema\Blueprint;

class Units implements Table
{
    /**
     * @return string
     */
    public function getTableName(): string
    {
        return (new Unit())->getTable();
    }

    /**
     * @param Blueprint $table
     *
     * @return void
     */
    public function onCreate(Blueprint $table)
    {
        $table->collation = 'utf8mb4_unicode_ci';
        $table->charset = 'utf8mb4';
        $table->engine = 'InnoDB';

        $table->increments('id')->unsigned();
        $table->string('name', 25);
        $table->string('japanese_name', 45);
        $table->string('icon');
        $table->string('link_seesaw', 100)->nullable();
        $table->string('link_gc', 100)->nullable();
        $table->enum('rarity', Unit::getRarities());
        $table->enum('gender', Unit::getGenders());
        $table->boolean('dmm');
        $table->boolean('nutaku');
        $table->boolean('special_cg');
        $table->timestamps();
    }

}
