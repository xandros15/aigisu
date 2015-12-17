<?php
/**
 * PHP Imgur wrapper 0.1
 * Imgur API wrapper for easy use.
 * @author Vadim Kr.
 * @copyright (c) 2013 bndr
 * @license http://creativecommons.org/licenses/by-sa/3.0/legalcode
 */

namespace Imgur;

class Connect
{
    const TYPE_GET    = 'GET';
    const TYPE_POST   = 'POST';
    const TYPE_PUT    = 'PUT';
    const TYPE_DELETE = 'DELETE';

    /**
     * @var array
     */
    protected $options;

    /**
     * @var string
     */
    protected $api_key;

    /**
     * @var string
     */
    protected $api_secret;

    /**
     * @var string
     */
    protected $api_endpoint;

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
     * @var resource
     */
    protected $curl;

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * Constructor
     * @param string $api_key
     * @param string $api_secret
     */
    function __construct($api_key, $api_secret)
    {
        $this->api_key    = $api_key;
        $this->api_secret = $api_secret;
    }

    /**
     * Set Access Data. Used for authorization
     * @param $accessToken
     * @param $refreshToken
     */
    function setAccessData($accessToken, $refreshToken)
    {
        $this->access_token  = $accessToken;
        $this->refresh_token = $refreshToken;
    }

    /**
     * Make request to Imgur API endpoint
     * @param $endpoint
     * @param mixed $options
     * @param string $type
     * @return mixed
     * @throws Exception
     */
    function request($endpoint, array $options = [], $type = self::TYPE_GET)
    {
        $headers        = (!$this->access_token) ? array('Authorization: CLIENT-ID ' . $this->api_key) : array("Authorization: Bearer " . $this->access_token);
        $this->curl     = curl_init();
        $this->endpoint = $endpoint;
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);

        if ($options) {
            $this->setOptions($type, $options);
        }
        curl_setopt($this->curl, CURLOPT_URL, $this->endpoint);

        if (($data = curl_exec($this->curl)) === false) {
            throw new Exception(curl_error($this->curl));
        }
        curl_close($this->curl);

        if (!($json = json_decode($data, true))) {
            throw new Exception("The response shouldn't be null.");
        }
        return $json;
    }

    private function setOptions($type, $options)
    {
        switch ($type) {
            case self::TYPE_GET:
                $this->endpoint .= '?' . http_build_query($options);
                break;
            case self::TYPE_PUT:
            case self::TYPE_POST:
            case self::TYPE_DELETE:
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, $options);
                break;
        }
    }
}