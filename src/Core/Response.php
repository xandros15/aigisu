<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-01-21
 * Time: 02:01
 */

namespace Aigisu\Core;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Uri;

class Response extends \Slim\Http\Response
{
    /** @var string */
    private $basePath;

    /**
     * Response constructor.
     *
     * @param array $params
     * @param string $basePath
     */
    public function __construct(string $basePath, array $params = [])
    {
        parent::__construct($params['status'] ?? 200, $params['headers'] ?? null, $params['body'] ?? null);
        $this->basePath = $basePath;
    }

    /**
     * @param \Psr\Http\Message\UriInterface|string $url
     * @param null $status
     *
     * @return ResponseInterface
     */
    public function withRedirect($url, $status = null)
    {
        if ($this->basePath) {
            $url = Uri::createFromString($url)->withBasePath($this->basePath);
        }

        return parent::withRedirect($url, $status);
    }
}
