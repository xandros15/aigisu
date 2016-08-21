<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-19
 * Time: 23:29
 */

namespace Models;


use Aigisu\Model;
use Aigisu\Validator;
use Respect\Validation\Validator as v;

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
    use Validator;

    protected $fillable = [
        'name',
        'password_hash',
        'email',
        'access_token',
        'recovery_hash',
        'remember_identifier',
        'remember_hash',
    ];

    public function rules() : array
    {
        return [
            'name' => v::stringType()->length(4, 15),
            'email' => v::email(),
            'password' => v::stringType()->length(8, 32)
        ];
    }
}