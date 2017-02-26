<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-28
 * Time: 14:48
 */

namespace Aigisu\Api\Middlewares;


use Aigisu\Components\Http\BadRequestException;
use Aigisu\Core\MiddlewareInterface;
use Aigisu\Models\Unit;
use Aigisu\Models\Unit\CG;
use Aigisu\Models\Unit\MissingCG;
use Slim\Http\Request;
use Slim\Http\Response;

class MissingCGValidatorMiddleware implements MiddlewareInterface
{
    const MESSAGE = 'message';

    /** @var array */
    private $errors = [];

    /**
     * @param Request $request
     * @return bool
     */
    public function validate(Request $request) : bool
    {
        $params = $this->parseParams($request->getParams(), $request->getAttribute('id', 0));

        if (!$this->isMissing($request->getAttribute('unitId'), $params)) {
            $this->errors[] = 'This CG isn\'t required';
        }

        return !$this->errors;
    }

    /**
     * @return array
     */
    public function getErrors() : array
    {
        return $this->errors;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     * @throws BadRequestException
     */
    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        if (!$this->validate($request)) {
            $response = $response->withJson([
                self::MESSAGE => $this->getErrors(),
            ]);

            throw new BadRequestException($request, $response);
        }

        return $next($request, $response);
    }

    /**
     * @param int $unitId
     * @param array $params
     * @return bool
     */
    protected function isMissing(int $unitId, array $params) : bool
    {
        if ($params['is_changed'] && !$params['archival']) {
            return false;
        }

        $unit = Unit::with('cg')->findOrFail($unitId);
        $missing = new MissingCG($unit['cg']);
        $whatMissing = $missing->filter([
            'is_dmm' => $unit['dmm'],
            'is_nutaku' => $unit['nutaku'],
            'is_special_cg' => $unit['spacial_cg'],
        ]);

        foreach ($whatMissing as $missingCG) {
            if ($missingCG['server'] == $params['server'] && $missingCG['scene'] == $params['scene']) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $params
     * @param null|int $id
     * @return array
     */
    private function parseParams(array $params, $id = null) : array
    {
        $newParams = $this->getNewParams($params);
        $oldParams = $id ? $this->getOldParams($id) : [];
        $mergeParams = array_merge($oldParams, $newParams);
        $isChange = $mergeParams == $newParams;

        return $mergeParams + ['is_changed' => $isChange];
    }

    /**
     * @param array $params
     * @return array
     */
    private function getNewParams(array $params) : array
    {
        return [
            'server' => (string)($params['server'] ?? null),
            'scene' => (int)($params['scene'] ?? null),
            'archival' => (bool)($params['archival'] ?? null),
        ];
    }

    /**
     * @param int $cgId
     * @return array
     */
    private function getOldParams(int $cgId) : array
    {
        $cg = CG::findOrNew($cgId);
        return [
            'server' => (string)$cg['server'],
            'scene' => (int)$cg['scene'],
            'archival' => (bool)$cg['archival'],
        ];

    }
}
