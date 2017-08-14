<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-31
 * Time: 21:05
 */

namespace Aigisu\Data\Tables;


use Aigisu\Core\Configuration;
use Aigisu\Models\User;
use Google\Auth\Cache\InvalidArgumentException;
use Illuminate\Database\Schema\Blueprint;

class Users implements Table
{

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return (new User())->getTable();
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
        $table->string('name', 15)->unique();
        $table->string('password', 255);
        $table->string('email', 64)->unique();
        $table->enum('role', $this->getEnumRoles())->default($this->getDefaultRole());
        $table->boolean('is_confirmed')->default(false);
        $table->string('recovery_hash', 255)->nullable();
        $table->string('remember_identifier', 255)->nullable();
        $table->string('remember_hash', 255)->nullable();
        $table->string('refresh_token', 255)->nullable();
        $table->timestamps();
    }

    /**
     * @return array
     */
    private function getEnumRoles(): array
    {
        $accesses = (new Configuration())->settings['access'];
        $roles = [];
        foreach ($accesses as $access) {
            $roles[$access['level']] = $access['role'];
        }

        if (!$roles) {
            throw new InvalidArgumentException('Missing roles in access param. Check configuration params');
        }

        return $roles;
    }

    public function getDefaultRole(): string
    {
        $enum = $this->getEnumRoles();
        ksort($enum);

        return end($enum);
    }
}
