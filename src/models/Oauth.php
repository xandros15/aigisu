<?php

namespace models;

use Illuminate\Database\Eloquent\Model;
use traits\Validator;

/**
 * Class Oauth
 */

/**
 * @property int $time
 * @property string $pin
 * @property string $token
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