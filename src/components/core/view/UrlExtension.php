<?php
namespace Aigisu\View;

use Aigisu\ActiveContainer;

/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-06-18
 * Time: 03:11
 */

/**
 * @property string siteUrl
 */
class UrlExtension extends ActiveContainer implements ViewExtension
{
    public function pathFor($name, $data = [], $queryParams = [])
    {
        return $this->router->pathFor($name, $data, $queryParams);
    }

    public function getQuery($name, $default = '')
    {
        return $this->request->getParam($name, $default);
    }

    public function getSiteUrl()
    {
        return $this->siteUrl;
    }

    public function applyCallbacks(CallbackManager &$callbackManager)
    {
        $callbackManager->addCallback('pathFor', [$this, 'pathFor']);
        $callbackManager->addCallback('siteUrl', [$this, 'getSiteUrl']);
        $callbackManager->addCallback('query', [$this, 'getQuery']);
    }
}