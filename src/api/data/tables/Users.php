<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-31
 * Time: 21:05
 */

namespace Aigisu\Api\Data\Tables;


use Aigisu\Api\Models\User;
use Illuminate\Database\Schema\Blueprint;

class Users implements Table
{

    /**
     * @return string
     */
    public function getTableName() : string
    {
        return (new User())->getTable();
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
        $table->string('name', 15)->unique();
        $table->string('password', 255);
        $table->string('email', 64)->unique();;
        $table->string('access_token', 255)->nullable();
        $table->dateTime('token_expire')->nullable();
        $table->string('recovery_hash', 255)->nullable();
        $table->string('remember_identifier', 255)->nullable();
        $table->string('remember_hash', 255)->nullable();
        $table->timestamps();
    }

}