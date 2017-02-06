<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-19
 * Time: 23:29
 */

namespace Aigisu\Models;


use Aigisu\Core\Model;

/**
 * @property string $name
 * @property string $password
 * @property string $email
 * @property string $role
 * @property string $recovery_hash
 * @property string $remember_identifier
 * @property string $remember_hash
 */
class User extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * @param string $password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * @param string $password
     * @return bool
     */
    public function validatePassword(string $password) : bool
    {
        return password_verify($password, $this->password);
    }
}
