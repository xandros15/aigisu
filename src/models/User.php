<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-19
 * Time: 23:29
 */

namespace Models;


use Aigisu\Model;

class User extends Model
{
    protected $fillable = [
        'name',
        'password',
        'email',
        'access_token',
        'recovery_hash',
        'remember_identifier',
        'remember_hash',
    ];
}