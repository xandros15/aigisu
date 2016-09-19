<?php

namespace Aigisu\Common\Controllers;

use Aigisu\Common\Components\Alert\Alert;
use Aigisu\Common\Components\Http\Client;
use Aigisu\Common\Components\View\View;
use Aigisu\Core\ActiveContainer;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class Controller
 * @package Aigisu\Common\Controllers
 *
 * @property View $view
 */
abstract class Controller extends ActiveContainer
{
    const LAYOUT = 'layout/main';
    const INDEX = 'id';

    /**
     * @param Response $response
     * @param string $view
     * @param array $params
     * @return Response
     */
    public function render(Response $response, string $view, array $params = []) : Response
    {
        $renderer = $this->get(View::class);
        $content = $renderer->render($view, $params);

        if ($view !== self::LAYOUT) {
            $content = $renderer->render(self::LAYOUT, ['content' => $content]);
        }

        return $response->write($content);
    }

    /**
     * @param Response $response
     * @param $view
     * @param array $params
     * @return Response
     */
    public function renderAjax(Response $response, string $view, array $params = []) : Response
    {
        $render = $this->get(View::class)->render($view, $params);

        return $response->write($render);
    }

    /**
     * @return Response
     */
    public function goBack() : Response
    {
        if ($this->request->hasHeader('HTTP_REFERER')) {
            $referer = $this->request->getHeader('HTTP_REFERER')[0];
            return $this->response->withRedirect($referer, 301);
        }
        return $this->goHome();
    }

    /**
     * @return Response
     */
    public function goHome() : Response
    {
        return $this->response->withRedirect($this->siteUrl, 301);
    }

    /**
     * @param Response $response
     * @return Client
     */
    protected function makeClient(Response $response) : Client
    {
        $client = new Client($response, ['base_uri' => $this->siteUrl,]);

        return $client;
    }

    /**
     * @param Request $request
     * @return int
     */
    protected function getID(Request $request): int
    {
        return $request->getAttribute(self::INDEX, 0);
    }

    protected function addAlertIfError(ResponseInterface $response)
    {
        if ($response->getStatusCode() === 400) {
            $json = json_decode((string) $response->getBody(), true);
            $message = function (array $errors): string {
                $text = '';
                foreach ($errors as $name => $error) {
                    $text .= $name . ':' . PHP_EOL;
                    foreach ($error as $item) {
                        $text .= '- ' . $item . PHP_EOL;
                    }
                }

                return $text;
            };

            Alert::add($message($json['message']), Alert::ERROR);
            return true;
        }

        return false;
    }
}