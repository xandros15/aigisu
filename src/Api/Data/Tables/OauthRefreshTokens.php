<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-08
 * Time: 16:32
 */

namespace Aigisu\Api\Data\Tables;


use Illuminate\Database\Schema\Blueprint;

class OauthRefreshTokens implements Table
{

    /**
     * @return string
     */
    public function getTableName() : string
    {
        return 'oauth_refresh_tokens';
    }

    /**
     * @param Blueprint $table
     * @return void
     */
    public function onCreate(Blueprint $table)
    {
        $table->string('id', 100)->primary();
        $table->string('access_token_id', 100)->index();
        $table->boolean('revoked');
        $table->dateTime('expires_at')->nullable(); // <- say wat, Otwell?
    }
}
