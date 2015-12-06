<?php

namespace app\imgur;

use Imgur\Imgur as Base;
use Imgur\Authorize;
use Exception;
use app\ArrayException;

class Imgur extends Base
{
    const KEY_PATH         = CONFIG_DIR . 'imgur.key.json';
    const CREDENTIALS_PATH = CONFIG_DIR . 'imgur.credentials.json';
    const ALBUM_PATH       = CONFIG_DIR . 'imgur.albums.json';

    public $description;
    public $title;
    public $album;
    public $filename;
    public $credentials;
    protected static $albums = [];

    /**
     * @var Authorize 
     */
    protected $auth;

    public function __construct($api_key, $api_secret)
    {

        parent::__construct($api_key, $api_secret);
    }

    /**
     * @return \app\imgur\Imgur
     * @throws Exception if token file no exist
     */
    public static function facade()
    {
        if (!is_file(self::KEY_PATH)) {
            throw new Exception('The json token no exits. Create it first by createKeyToken method');
        }
        if (is_file(self::ALBUM_PATH)) {
            static::setAlbums();
        }
        $token = json_decode(file_get_contents(self::KEY_PATH));
        $imgur = new Imgur($token->api_key, $token->api_secret);
        $imgur->authorize();
        return $imgur;
    }

    /**
     * @param string $key
     * @param string $secret
     * @return object JSON
     */
    public static function createKeyToken($key, $secret)
    {
        $token = json_encode(['api_key' => $key, 'api_secret' => $secret]);
        if (!file_exists(dirname(self::KEY_PATH))) {
            mkdir(dirname(self::KEY_PATH), 0700, true);
        }
        return (file_put_contents(self::KEY_PATH, $token));
    }

    public function authorize()
    {
        $this->auth = new Authorize($this->conn, $this->api_key, $this->api_secret);
        if (!is_file(self::CREDENTIALS_PATH)) {
            $this->createTokens();
        } else {
            $credentials = file_get_contents(self::CREDENTIALS_PATH);
            $this->setCredentials($credentials);
        }
        if ($this->isExpired()) {
            $this->refreshTokens();
        }
        $this->setAccessData();
    }

    public function uploadFile()
    {
        $options = ['type' => 'file'];
        if ($this->title) {
            $options['title'] = $this->title;
        }
        if ($this->album) {
            $options['album'] = $this->album;
        }
        if ($this->description) {
            $options['description'] = $this->description;
        }
        $response = $this->upload()->file($this->filename, $options);
        if (!$response || !empty($response['success'])) {
            throw new ArrayException($response);
        }
        return $response;
    }

    public function setDescription($description)
    {
        $this->description = (is_array($description)) ? self::parseDiscription($description) : $description;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setAlbum($album)
    {
        if (!isset(self::$albums[$album])) {
            throw new Exception("The album: '{$album}' no exists");
        }
        $this->album = $album;
    }

    public function setFilename($filename)
    {
        if (!is_file($filename)) {
            throw new Exception("The file: '{$filename}' no exists");
        }
        $this->filename = $filename;
    }

    private static function setAlbums()
    {
        static::$albums = json_decode(file_get_contents(self::ALBUM_PATH));
    }

    private function setAccessData()
    {
        $this->conn->setAccessData($this->credentials->access_token, $this->credentials->refresh_token);
    }

    private function createTokens()
    {
        $response = $this->auth->getAuthorizationPin();
        if (!isset($response['created_at'])) {
            $response['created_at'] = time();
        }
        if (is_array($response)) {
            $response = json_encode($response);
        }
        if (!file_exists(dirname(self::CREDENTIALS_PATH))) {
            mkdir(dirname(self::CREDENTIALS_PATH), 0700, true);
        }
        file_put_contents(self::CREDENTIALS_PATH, $response);
        $this->setCredentials($response);
    }

    private function setCredentials($credentials)
    {
        $this->credentials = json_decode($credentials);
    }

    private function isExpired()
    {
        return ((time() - $this->credentials->created_at) > $this->credentials->expires_in);
    }

    private function refreshTokens()
    {
        $response = $this->auth->refreshAccessToken($this->credentials->refresh_token);
        if (!isset($response['created_at'])) {
            $response['created_at'] = time();
        }
        if (is_array($response)) {
            $response = json_encode($response);
        }
        file_put_contents(self::CREDENTIALS_PATH, $response);
        $this->setCredentials($response);
    }

    private static function parseDiscription(array $discription)
    {
        $string = '';
        foreach ($discription as $name => $value) {
            $string .= $name . ': ' . $value . ', ';
        }
        return rtrim($string, ', ');
    }
}