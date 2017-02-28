<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-05
 * Time: 21:19
 */

namespace Aigisu\Api\Controllers\Unit\CG;


use Aigisu\Api\Controllers\AbstractController;
use Aigisu\Models\Unit\CG;
use RuntimeException;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class AbstractUploader extends AbstractController
{
    const UNIT_ID = 'unitId';

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public abstract function actionCreate(Request $request, Response $response) : Response;

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public abstract function actionUpdate(Request $request, Response $response) : Response;

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public abstract function actionDelete(Request $request, Response $response) : Response;

    /**
     * @param CG $cg
     * @throws RuntimeException
     * @return string
     */
    protected function getImageFileName(CG $cg) : string
    {
        if (!file_exists($filename = $this->get('upload') . '/' . $cg->getOriginal('local'))) {
            throw new FileNotFoundException("File {$filename} doesn't exist");
        }

        return $filename;
    }
}
