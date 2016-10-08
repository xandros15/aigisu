<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-06
 * Time: 17:28
 */

namespace Aigisu\Components\Oauth;


use Illuminate\Database\Connection;

abstract class AbstractOauthConnection
{
    /**
     * The database connection.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $database;

    /**
     * Create a new repository instance.
     *
     * @param  \Illuminate\Database\Connection  $database
     */
    public function __construct(Connection $database)
    {
        $this->database = $database;
    }
}