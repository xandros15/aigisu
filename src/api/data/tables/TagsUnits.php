<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-31
 * Time: 21:46
 */

namespace Aigisu\Api\Data\Tables;


use Aigisu\Api\Data\Schema;
use Aigisu\Api\Models\Unit;
use Aigisu\Api\Models\Unit\Tag;
use Illuminate\Database\Schema\Blueprint;

class TagsUnits implements Table
{
    /**
     * @return string
     */
    public function getTableName() : string
    {
        return Schema::pivot(new Tag(), new Unit());
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

        $table->integer('unit_id')->unsigned()->index();
        $table->integer('tag_id')->unsigned()->index();
        $table->foreign('unit_id')->references('id')->on((new Units())->getTableName())->onDelete('cascade');
        $table->foreign('tag_id')->references('id')->on((new Tags)->getTableName())->onDelete('cascade');
    }
}