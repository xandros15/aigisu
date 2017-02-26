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

class MissingCGValidator implements ValidatorInterface
{

    /** @var array */
    private $errors = [];

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
        $params = $this->parseParams($params, $params['_attributes']['id'] ?? 0);

        if (!$this->isMissing($params['_attributes']['unitId'] ?? 0, $params)) {
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
