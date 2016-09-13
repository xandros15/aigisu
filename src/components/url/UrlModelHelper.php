<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-14
 * Time: 00:02
 */

namespace Aigisu\Components\Url;

use Aigisu\Core\Model;

/**
 * Class UrlModelHelper
 * @package Aigisu\Components\Api
 * @mixin Model
 */
trait UrlModelHelper
{
    /** @var UrlManager|null */
    private static $urlManager;

    /**
     * @param UrlManager $urlManager
     */
    public static function setUrlManager(UrlManager $urlManager)
    {
        static::$urlManager = $urlManager;
    }

    /**
     * @param string $name
     * @param array $params
     * @param array $query
     * @return string
     */
    public function urlTo(string $name, array $params = [], array $query = []) : string
    {
        return static::$urlManager ? static::$urlManager->to($name, $params, $query) : '';
    }
}