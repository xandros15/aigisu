<?php
/**
 * PHP Imgur wrapper 0.1
 * Imgur API wrapper for easy use.
 * @author Vadim Kr.
 * @copyright (c) 2013 bndr
 * @license http://creativecommons.org/licenses/by-sa/3.0/legalcode
 */

namespace Imgur;

class Authorize
{
    const RESPONSE_PIN       = 'pin';
    const RESPONSE_CODE      = 'code';
    const ENDPOINT_TOKEN     = '/token/';
    const ENDPOINT_AUTHORIZE = '/authorize/';
    const GRAND_TYPE_CODE    = 'authorization_code';
    const GRAND_TYPE_PIN     = 'pin';

    /**
     * @var string
     */
    /**
     * @var string
     */
    protected $api_key, $api_secret;

    /**
     * @var Connect
     */
    protected $connection;

    /**
     * @var string
     */
    protected $access_token;

    /**
     * @var string
     */
    protected $refresh_token;

    /**
     * @var string
     */
    protected $oauth = "https://api.imgur.com/oauth2";

    /**
     * Constructor
     * @param Connect $connection
     * @param string $key
     * @param string $secret
     */
    function __construct($connection, $key, $secret)
    {
        $this->api_key    = $key;
        $this->api_secret = $secret;
        $this->connection = $connection;
    }

    /**
     * Set Access data for future uses.
     * @param $accessToken
     * @param $refreshToken
     */
    function setAccessData($accessToken, $refreshToken)
    {
        $this->access_token  = $accessToken;
        $this->refresh_token = $refreshToken;
    }

    /**
     * Exchange authorization code for an access token
     * @param string $code
     * @return array $response
     */
    function getAccessToken($code)
    {
        $uri      = $this->oauth . self::ENDPOINT_TOKEN;
        $options  = array(
            'client_id' => $this->api_key,
            'client_secret' => $this->api_secret,
            'grant_type' => self::GRAND_TYPE_CODE,
            'code' => $code
        );
        $response = ($code) ? $this->connection->request($uri, $options, Connect::TYPE_POST) : null;

        return $response;
    }

    /**
     * Exchange the refresh token for access token
     * @param string $refresh_token
     * @return array $response
     */
    function refreshAccessToken($refresh_token)
    {

        $uri     = $this->oauth . self::ENDPOINT_TOKEN;
        $options = array(
            'client_id' => $this->api_key,
            'client_secret' => $this->api_secret,
            'grant_type' => 'refresh_token',
            'refresh_token' => $refresh_token
        );

        $response = ($refresh_token) ? $this->connection->request($uri, $options, Connect::TYPE_POST) : null;

        return $response;
    }

    /**
     * Show the authorization page to the user
     */
    function getAuthorizationCode()
    {
        $options = [
            'client_id' => $this->api_key,
            'response_type' => self::RESPONSE_CODE,
            'state' => 'initializing'
        ];
        $uri     = $this->oauth . self::ENDPOINT_AUTHORIZE . '?' . http_build_query($options);
        echo "<!doctype html><html><head><meta charset='utf-8'></head>
            <body><a href='{$uri}' target='_blank'>Click this link to authorize the application to access your Imgur data</a><br>
               </body></html>";

        exit;
    }

    function getAuthorizationPin()
    {
        $clientOptions = [
            'client_id' => $this->api_key,
            'response_type' => self::RESPONSE_PIN
        ];
        $clientUri     = $this->oauth . self::ENDPOINT_AUTHORIZE . '?' . http_build_query($clientOptions);

        printf("Open the following link in your browser:\n%s\n", $clientUri);
        print 'Enter verification code: ';
        $pin = trim(fgets(STDIN));

        return $this->getAccessTokenByPin($pin);
    }

    /**
     * Exchange authorization code for an access token
     * @param string $pin
     * @return array $response
     */
    function getAccessTokenByPin($pin)
    {
        $uri = $this->oauth . self::ENDPOINT_TOKEN;
        $options = array(
            'client_id' => $this->api_key,
            'client_secret' => $this->api_secret,
            'grant_type' => self::GRAND_TYPE_PIN,
            'pin' => $pin
        );
        $response = ($pin) ? $this->connection->request($uri, $options, Connect::TYPE_POST) : null;

        return $response;
    }
}