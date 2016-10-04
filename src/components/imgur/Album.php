<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-04
 * Time: 03:41
 */

namespace Aigisu\Components\Imgur;


use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

class Album extends AbstractApi
{
    Const
        ENDPOINT_ALBUM = 'https://api.imgur.com/3/album/{id}',
        ENDPOINT_ALBUM_IMAGES = 'https://api.imgur.com/3/album/{id}/images',
        ENDPOINT_ALBUM_IMAGE = 'https://api.imgur.com/3/album/{album_id}/image/{image_id}',
        ENDPOINT_ALBUM_CREATE = 'https://api.imgur.com/3/album',
        ENDPOINT_ALBUM_UPDATE = 'https://api.imgur.com/3/album/{id}',
        ENDPOINT_ALBUM_DELETE = 'https://api.imgur.com/3/album/{id}',
        ENDPOINT_ALBUM_FAVORITE = 'https://api.imgur.com/3/album/{id}/favorite',
        ENDPOINT_ALBUM_IMAGES_SET = 'https://api.imgur.com/3/album/{id}',
        ENDPOINT_ALBUM_IMAGES_ADD = 'https://api.imgur.com/3/album/{id}/add',
        ENDPOINT_ALBUM_IMAGES_REMOVE = 'https://api.imgur.com/3/album/{id}/remove_images';

    /**
     * @param string $id
     * @return ResponseInterface
     * @see https://api.imgur.com/endpoints/album#album
     */
    public function get(string $id) : ResponseInterface
    {
        $uri = strtr(self::ENDPOINT_ALBUM, ['{id}' => $id]);
        $request = new Request('GET', $uri);

        return $this->client->execute($request);
    }

    /**
     * @param string $id
     * @return ResponseInterface
     * @see https://api.imgur.com/endpoints/album#album-images
     */
    public function images(string $id): ResponseInterface
    {
        $uri = strtr(self::ENDPOINT_ALBUM_IMAGES, ['{id}' => $id]);
        $request = new Request('GET', $uri);

        return $this->client->execute($request);
    }

    /**
     * @param string $albumId
     * @param string $imageId
     * @return ResponseInterface
     * @see https://api.imgur.com/endpoints/album#album-image
     */
    public function image(string $albumId, string $imageId): ResponseInterface
    {
        $uri = strtr(self::ENDPOINT_ALBUM_IMAGE, [
            '{album_id}' => $albumId,
            '{image_id}' => $imageId,
        ]);

        $request = new Request('GET', $uri);

        return $this->client->execute($request);
    }

    /**
     * @param array $params
     * @return ResponseInterface
     * @see https://api.imgur.com/endpoints/album#album-upload
     */
    public function create(array $params = []): ResponseInterface
    {
        $defaults = ['ids', 'title', 'description', 'privacy', 'layout', 'cover'];
        $params = $this->proceedParams($params, $defaults);

        $body = new MultipartStream($params);
        $request = new Request('POST', self::ENDPOINT_ALBUM_CREATE, [], $body);

        return $this->client->execute($request);
    }

    /**
     * @param string $id
     * @param array $params
     * @return ResponseInterface
     * @see https://api.imgur.com/endpoints/album#album-update
     */
    public function update(string $id, array $params = []): ResponseInterface
    {
        $defaults = ['ids', 'title', 'description', 'privacy', 'layout', 'cover'];
        $params = $this->proceedParams($params, $defaults);

        $uri = strtr(self::ENDPOINT_ALBUM_UPDATE, ['{id}' => $id]);

        $body = new MultipartStream($params);
        $request = new Request('PUT', $uri, [], $body);

        return $this->client->execute($request);
    }

    /**
     * @param string $id
     * @return ResponseInterface
     * @see https://api.imgur.com/endpoints/album#album-delete
     */
    public function delete(string $id): ResponseInterface
    {
        $uri = strtr(self::ENDPOINT_ALBUM_DELETE, ['{id}' => $id]);
        $request = new Request('DELETE', $uri);

        return $this->client->execute($request);
    }

    /**
     * @param string $id
     * @return ResponseInterface
     * @see https://api.imgur.com/endpoints/album#album-favorite
     */
    public function favorite(string $id): ResponseInterface
    {
        $uri = strtr(self::ENDPOINT_ALBUM_FAVORITE, ['{id}' => $id]);
        $request = new Request('POST', $uri);

        return $this->client->execute($request);
    }

    /**
     * @param string $id
     * @param array $imagesIds
     * @return ResponseInterface
     * @see https://api.imgur.com/endpoints/album#album-set-to
     */
    public function setImages(string $id, array $imagesIds): ResponseInterface
    {
        $uri = strtr(self::ENDPOINT_ALBUM_IMAGES_SET, ['{id}' => $id]);
        $params = $this->proceedParams(['ids' => $imagesIds], ['ids']);
        $body = new MultipartStream($params);
        $request = new Request('POST', $uri, [], $body);

        return $this->client->execute($request);
    }

    /**
     * @param string $id
     * @param array $imagesIds
     * @return ResponseInterface
     * @see https://api.imgur.com/endpoints/album#album-add-to
     */
    public function addImages(string $id, array $imagesIds): ResponseInterface
    {
        $uri = strtr(self::ENDPOINT_ALBUM_IMAGES_ADD, ['{id}' => $id]);
        $params = $this->proceedParams(['ids' => $imagesIds], ['ids']);
        $body = new MultipartStream($params);
        $request = new Request('PUT', $uri, [], $body);

        return $this->client->execute($request);
    }

    /**
     * @param string $id
     * @param array $imagesIds
     * @return ResponseInterface
     * @see https://api.imgur.com/endpoints/album#album-remove-from
     */
    public function removeImages(string $id, array $imagesIds): ResponseInterface
    {
        $uri = strtr(self::ENDPOINT_ALBUM_IMAGES_REMOVE, ['{id}' => $id]);
        $params = $this->proceedParams(['ids' => $imagesIds], ['ids']);
        $body = new MultipartStream($params);
        $request = new Request('DELETE', $uri, [], $body);

        return $this->client->execute($request);
    }
}