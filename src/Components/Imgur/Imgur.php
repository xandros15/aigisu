<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-04
 * Time: 13:24
 */

namespace Aigisu\Components\Imgur;


use Aigisu\Components\Configure\Configurable;
use Psr\Http\Message\ResponseInterface;

class Imgur extends Configurable
{
    /** @var ClientInterface */
    private $client;

    /**
     * Imgur constructor.
     * @param ClientInterface $client
     * @param array $params
     */
    public function __construct(ClientInterface $client, array $params = [])
    {
        $this->client = $client;
        parent::__construct($params);
    }

    /**
     * @param $file
     * @param array $params
     * @return ResponseInterface
     */
    public function uploadDmmImage($file, array $params = []) : ResponseInterface
    {
        $image = new Image($this->client);
        $params = array_merge($params, ['album' => $this->config['albums']['dmm']]);

        return $image->upload($file, $params);
    }

    /**
     * @param $file
     * @param array $params
     * @return ResponseInterface
     */
    public function uploadNutakuImage($file, array $params = []) : ResponseInterface
    {
        $image = new Image($this->client);
        $params = array_merge($params, ['album' => $this->config['albums']['nutaku']]);

        return $image->upload($file, $params);
    }

    /**
     * @param string $id
     * @return ResponseInterface
     */
    public function deleteImage(string $id) : ResponseInterface
    {
        $image = new Image($this->client);

        return $image->delete($id);
    }
}
