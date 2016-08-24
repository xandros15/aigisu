<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-19
 * Time: 23:29
 */

namespace Models;


use Aigisu\Model;

/**
 * @property string $name
 * @property string $password_hash
 * @property string $email
 * @property string $access_token
 * @property string $recovery_hash
 * @property string $remember_identifier
 * @property string $remember_hash
 */
class User extends Model
{
    public $password;
    protected $fillable = [
        'name',
        'email'
    ];

    public function encryptPassword()
    {
        $this->password_hash = password_hash($this->password, PASSWORD_DEFAULT);
    }
}