<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-28
 * Time: 14:48
 */

namespace Aigisu\Components\Validators;


use Aigisu\Models\Unit;
use Aigisu\Models\Unit\CG;
use Aigisu\Models\Unit\MissingCG;

class MissingCGValidator extends AbstractValidator
{

    /**
     * @param array $params
     * @return bool
     */
    public function validateFiles(array $params) : bool
    {
        return true;
    }

    /**
     * @param array $params
     * @return bool
     */
    public function validate(array $params) : bool
    {
        $unitId = $this->getUnitId($params);
        $cgId = $this->getCGId($params);
        $params = $this->getParams($params, $cgId);

        if ($params['is_changed'] && !$params['archival']) {
            if (!$this->isMissing($unitId, $params)) {
                $this->errors[] = 'This CG isn\'t required';
            }
        }

        return !$this->errors;
    }

    public function getUnit(int $id)
    {
        return Unit::with('cg')->findOrFail($id)->toArray();
    }

    public function getCG(int $id)
    {
        return CG::findOrFail($id)->toArray();
    }

    /**
     * @param array $params
     * @return int
     */
    protected function getCGId(array $params) : int
    {
        return $params['_attributes']['id'] ?? 0;
    }

    /**
     * @param array $params
     * @return int
     */
    protected function getUnitId(array $params) : int
    {
        return $params['_attributes']['unitId'] ?? 0;
    }

    /**
     * @param int $unitId
     * @param array $params
     * @return bool
     */
    protected function isMissing(int $unitId, array $params) : bool
    {
        $unit = $this->getUnit($unitId);

        $missing = new MissingCG($unit['cg']);
        $whatMissing = $missing->filter($unit);

        foreach ($whatMissing as $missingCG) {
            if ($missingCG['server'] == $params['server'] &&
                $missingCG['scene'] == $params['scene']
            ) {
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
    private function getParams(array $params, $id = null) : array
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
        $cg = $this->getCG($cgId);
        return [
            'server' => (string)$cg['server'],
            'scene' => (int)$cg['scene'],
            'archival' => (bool)$cg['archival'],
        ];

    }
}
