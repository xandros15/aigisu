<?php

namespace Models;

use Aigisu\Model;
use Traits\Validator;

/**
 * Class Oauth
 */

/**
 * @property int $time
 * @property string $pin
 * @property string $token
 * @property int id
 */
class Oauth extends Model
{

    use Validator;
    public $timestamps  = false;
    protected $table    = 'oauth';
    protected $fillable = [
        'time',
        'pin',
        'token'
    ];
    protected $guarded  = [];

    public function validateTime()
    {
        return ($this->time - time() > 0);
    }
}