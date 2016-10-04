<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-03
 * Time: 21:33
 */

namespace Aigisu\Components\Imgur;


use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use function GuzzleHttp\Psr7\build_query;
use function GuzzleHttp\Psr7\stream_for;

class Image extends AbstractApi
{
    Const
        ENDPOINT_IMAGE = 'https://api.imgur.com/3/image/{id}',
        ENDPOINT_IMAGE_UPLOAD = 'https://api.imgur.com/3/image',
        ENDPOINT_IMAGE_DELETE = 'https://api.imgur.com/3/image/{id}',
        ENDPOINT_IMAGE_UPDATE = 'https://api.imgur.com/3/image/{id}',
        ENDPOINT_IMAGE_FAVORITE = 'https://api.imgur.com/3/image/{id}/favorite';

    /**
     * @param string $id
     * @see https://api.imgur.com/endpoints/image#image
     * @return ResponseInterface
     */
    public function get(string $id) : ResponseInterface
    {
        $uri = strtr(self::ENDPOINT_IMAGE, ['{id}' => $id]);
        $request = new Request('GET', $uri);

        return $this->client->execute($request);
    }

    /**
     * @param $file
     * @param array $params
     * @see https://api.imgur.com/endpoints/image#image-upload
     * @return ResponseInterface
     */
    public function upload($file, array $params = []) : ResponseInterface
    {
        $default = ['image', 'album', 'type', 'name', 'title', 'description'];
        $params = $this->proceedParams(array_merge($params, $this->proceedImage($file)), $default);

        $body = new MultipartStream($params);
        $request = new Request('POST', self::ENDPOINT_IMAGE_UPLOAD, [], $body);

        return $this->client->execute($request);
    }

    /**
     * Detects if image is file, url address or base64 string
     *
     * @param $image
     * @see https://api.imgur.com/endpoints/image#image-upload
     * @return array
     */
    private function proceedImage(string $image) : array
    {
        if (file_exists($image)) {
            //binary
            $params = [
                'image' => fopen($image, 'r'),
                'type' => 'file'
            ];
        } elseif (filter_var($image, FILTER_VALIDATE_URL)) {
            //url
            $params = [
                'image' => $image,
                'type' => 'URL'
            ];
        } else {
            //base64
            $params = [
                'image' => $image,
                'type' => 'base64'
            ];
        }

        return $params;
    }

    /**
     * @param string $id
     * @see https://api.imgur.com/endpoints/image#image-delete
     * @return ResponseInterface
     */
    public function delete(string $id) : ResponseInterface
    {
        $uri = strtr(self::ENDPOINT_IMAGE_DELETE, ['{id}' => $id]);
        $request = new Request('DELETE', $uri);

        return $this->client->execute($request);
    }

    /**
     * @param string $id
     * @param array $params
     * @see https://api.imgur.com/endpoints/image#image-update
     * @return ResponseInterface
     */
    public function update(string $id, array $params = []) : ResponseInterface
    {
        $uri = strtr(self::ENDPOINT_IMAGE_UPDATE, ['{id}' => $id]);
        $default = ['title', 'description'];
        $params = $this->proceedParams($params, $default);

        $body = new MultipartStream($params);
        $request = new Request('POST', $uri, [], $body);

        return $this->client->execute($request);
    }

    /**
     * @param string $id
     * @see https://api.imgur.com/endpoints/image#image-favorite
     * @return ResponseInterface
     */
    public function favorite(string $id) : ResponseInterface
    {
        $uri = strtr(self::ENDPOINT_IMAGE_FAVORITE, ['{id}' => $id]);
        $request = new Request('POST', $uri);

        return $this->client->execute($request);
    }
}