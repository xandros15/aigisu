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
 * @property string $password
 * @property string $email
 * @property string $access_token
 * @property string $recovery_hash
 * @property string $remember_identifier
 * @property string $remember_hash
 */
class User extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    protected $hidden = [
        'password',
        'access_token',
        'recovery_hash',
        'remember_identifier',
        'remember_hash',
    ];

    /**
     * @param string $password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = password_hash($password, PASSWORD_DEFAULT);
    }
}