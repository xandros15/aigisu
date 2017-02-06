<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-28
 * Time: 14:48
 */

namespace Aigisu\Api\Middlewares\Validators;


use Aigisu\Api\Middlewares\Validator;
use Aigisu\Models\Unit;
use Aigisu\Models\Unit\CG;
use Slim\Http\Request;

class MissingCGValidator extends Validator
{
    /**
     * @param Request $request
     * @return bool
     */
    public function validateFiles(Request $request) : bool
    {
        return true;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function validate(Request $request) : bool
    {
        $params = $this->getParams($request, $this->getCGId($request));

        if ($params['is_changed'] && !$params['archival']) {
            /** @var $unit Unit */
            $unit = Unit::findOrFail($this->getUnitId($request));
            if (!$this->isMissing($unit, $params)) {
                $this->errors[] = 'This CG isn\'t required';
            }
        }

        return !$this->errors;
    }

    /**
     * @param Request $request
     * @return int|null
     */
    protected function getCGId(Request $request)
    {
        $route = $request->getAttribute('route');
        return $route->getArgument('id');
    }

    /**
     * @param Request $request
     * @return int|null
     */
    protected function getUnitId(Request $request)
    {
        $route = $request->getAttribute('route');
        return $route->getArgument('unitId');
    }

    /**
     * @param Unit $unit
     * @param array $params
     * @return bool
     */
    protected function isMissing(Unit $unit, array $params) : bool
    {
        foreach ($unit->getAttribute('missingCG') as $missingCG) {
            if ($missingCG['server'] == $params['server'] &&
                $missingCG['scene'] == $params['scene']
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return array
     */
    private function getParams(Request $request, $id = null) : array
    {
        $newParams = $this->getNewParams($request);
        $isChanged = false;

        if ($id !== null) {
            $oldParams = $this->getOldParams($request);
            foreach ($newParams as $param => $value) {
                if ($value === null) {
                    $newParams[$param] = $oldParams[$param];
                }

                if ($oldParams[$param] != $newParams[$param]) {
                    $isChanged = true;
                }
            }
        } else {
            $isChanged = true;
        }

        $newParams['is_changed'] = $isChanged;

        return $newParams;
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getNewParams(Request $request) : array
    {
        return [
            'server' => $request->getParam('server'),
            'scene' => $request->getParam('scene'),
            'archival' => $request->getParam('archival'),
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getOldParams(Request $request) : array
    {
        $cg = CG::findOrFail($this->getCGId($request));
        return [
            'server' => $cg->getAttribute('server'),
            'scene' => $cg->getAttribute('scene'),
            'archival' => $cg->getAttribute('archival'),
        ];

    }
}
