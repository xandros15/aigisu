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
        'password_hash',
        'email',
        'access_token',
        'recovery_hash',
        'remember_identifier',
        'remember_hash',
    ];

    public function __construct(array $attributes = [])
    {
        $this->password = $attributes['password'] ?? '';
        $this->setEncryptPassword($this->password);
        parent::__construct($attributes);
    }

    private function setEncryptPassword(string $password)
    {
        $this->password_hash = password_hash($password, PASSWORD_DEFAULT);
    }
}