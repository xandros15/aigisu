<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-03
 * Time: 16:24
 */

namespace Aigisu\Components\Http\Handlers;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Handlers\AbstractHandler;
use Slim\Http\Body;
use Slim\Views\Twig;

class AccessDenied extends AbstractHandler implements HandlerInterface
{
    /** @var Twig */
    private $twig;

    /**
     * AccessDenied constructor.
     *
     * @param Twig $twig
     */
    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->getResponse($request, $response)->withStatus(403);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    protected function getResponse(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $contentType = $this->determineContentType($request);

        switch ($contentType) {
            case 'application/json':
                $response = $this->renderJsonOutput($response);
                break;
            case 'text/xml':
            case 'application/xml':
                $response = $this->renderXmlOutput($response);
                break;
            case 'text/html':
                $response = $this->renderHtmlOutput($response);
                break;
            default:
                throw new \UnexpectedValueException('Cannot render unknown content type ' . $contentType);
        }

        return $response->withHeader('Content-Type', $contentType);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    protected function renderJsonOutput(ResponseInterface $response): ResponseInterface
    {
        $body = new Body(fopen('php://temp', 'r+'));
        $body->write('{"message":"Forbidden"}');

        return $response->withBody($body);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    protected function renderXmlOutput(ResponseInterface $response): ResponseInterface
    {
        $body = new Body(fopen('php://temp', 'r+'));
        $body->write('<root><message>Forbidden</message></root>');

        return $response->withBody($body);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    protected function renderHtmlOutput(ResponseInterface $response): ResponseInterface
    {
        $body = new Body(fopen('php://temp', 'r+'));
        $response = $response->withBody($body);

        return $this->twig->render($response, 'errors/access-denied.twig');
    }
}
