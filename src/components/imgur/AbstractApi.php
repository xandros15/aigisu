<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-04
 * Time: 03:16
 */

namespace Aigisu\Components\Imgur;


abstract class AbstractApi
{
    /** @var Client */
    protected $client;

    /**
     * Image constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param array $params
     * @param array $default
     * @return array
     */
    protected function proceedParams(array $params, array $default) : array
    {
        $proceedParams = [];
        foreach ($default as $name) {
            if (isset($params[$name])) {
                $proceedParams[] = [
                    'name' => $name,
                    'contents' => $params[$name],
                ];
            }
        }

        return $proceedParams;
    }
}
