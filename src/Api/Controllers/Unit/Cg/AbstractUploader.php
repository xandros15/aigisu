<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-05
 * Time: 21:19
 */

namespace Aigisu\Api\Controllers\Unit\CG;


use Aigisu\Api\Controllers\Controller;
use Slim\Http\Request;

abstract class AbstractUploader extends Controller
{
    const UNIT_ID = 'unitId';

    protected function getLocation(Request $request)
    {
        return $this->router->pathFor('api.unit.cg.view', [
            'id' => $this->getID($request),
            'unitId' => $request->getAttribute('unitId')
        ]);
    }

    protected function getUnitID(Request $request)
    {
        return $request->getAttribute(self::UNIT_ID, 0);
    }
}
