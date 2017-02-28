<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-01-21
 * Time: 02:01
 */

namespace Aigisu\Core;

use Slim\Http\Uri;

class Response extends \Slim\Http\Response
{
    /** @var string */
    private $basePath;

    /**
     * Response constructor.
     * @param array $params
     * @param string $basePath
     */
    public function __construct(array $params, string $basePath)
    {
        parent::__construct($params['status'], $params['headers'] ?? null, $params['body'] ?? null);
        $this->basePath = $basePath;
    }

    /**
     * @param \Psr\Http\Message\UriInterface|string $url
     * @param null $status
     * @return static
     */
    public function withRedirect($url, $status = null)
    {
        if ($this->basePath) {
            $url = Uri::createFromString($url)->withBasePath($this->basePath);
        }

        return parent::withRedirect($url, $status);
    }
}
