<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-08
 * Time: 16:33
 */

namespace Aigisu\Api\Data\Tables;


use Illuminate\Database\Schema\Blueprint;

class OauthAccessTokens implements Table
{

    /**
     * @return string
     */
    public function getTableName() : string
    {
        return 'oauth_access_tokens';
    }

    /**
     * @param Blueprint $table
     * @return void
     */
    public function onCreate(Blueprint $table)
    {
        $table->string('id', 100)->primary();
        $table->integer('client_id')->index();
        $table->text('scopes')->nullable();
        $table->boolean('revoked');
        $table->timestamps();
        $table->dateTime('expires_at')->nullable();
    }
}
