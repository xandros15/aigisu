<?php


namespace Aigisu\Components\Validators;


use Aigisu\Components\Http\Exceptions\BadRequestException;
use Aigisu\Components\Validators\Rules\ImageSize;
use Aigisu\Components\Validators\Rules\StreamSize;
use Aigisu\Core\MiddlewareInterface;
use Psr\Http\Message\StreamInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator;
use Slim\Http\Request;
use Slim\Http\Response;

final class UploadIconValidatorMiddleware implements MiddlewareInterface
{
    const FIELD_NAME = 'icon';
    /** @var array */
    private $errors;

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     *
     * @return Response
     * @throws BadRequestException
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        if (!$this->validate($request->getBody())) {
            throw new BadRequestException($request, $response->withJson([
                'message' => [self::FIELD_NAME => $this->errors],
            ]));
        }

        return $next($request, $response);
    }

    /**
     * @param StreamInterface $input
     *
     * @return bool
     */
    private function validate(StreamInterface $input): bool
    {
        $validator = new Validator();
        try {
            return $validator
                ->addRule(new ImageSize(80, 150))
                ->addRule(new StreamSize('1KB', '50KB'))
                ->setName(self::FIELD_NAME)
                ->assert($input);
        } catch (NestedValidationException $exception) {
            $this->errors = $exception->getMessages();

            return false;
        }
    }
}
