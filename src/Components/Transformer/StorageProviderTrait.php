<?php


namespace Aigisu\Components\Transformer;


use Psr\Http\Message\UriInterface;
use Slim\Http\Uri;

trait StorageProviderTrait
{
    /** @var UriInterface */
    private $uri;

    /**
     * @param string|UriInterface $uri
     *
     * @return $this
     */
    public function setStorageUri($uri)
    {
        if (!$uri instanceof UriInterface) {
            $uri = Uri::createFromString($uri);
        }

        $this->uri = $uri;

        return $this;
    }

    /**
     * @return UriInterface|null
     */
    public function getStorageUri():? UriInterface
    {
        return $this->uri;
    }
}
