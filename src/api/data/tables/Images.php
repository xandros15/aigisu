<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-31
 * Time: 21:05
 */

namespace Aigisu\Api\Data\Tables;


use Aigisu\Api\Models\Image;
use Illuminate\Database\Schema\Blueprint;

class Images implements Table
{
    /**
     * @return string
     */
    public function getTableName() : string
    {
        return (new Image())->getTable();
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
        $table->string('md5', 32);
        $table->integer('unit_id', false, true);
        $table->enum('server', Image::getServersNames());
        $table->tinyInteger('scene', false, true);
        $table->string('google_id', 64);
        $table->string('imgur_id', 64);
        $table->string('imgur_delhash', 64);
        $table->timestamps();

        $table->foreign('unit_id')->references('id')->on((new Units())->getTableName())->onDelete('cascade');
    }

}