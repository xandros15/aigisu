<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-04
 * Time: 14:04
 */

namespace Aigisu\Api\Controllers\Unit\CG;


use Aigisu\Api\Controllers\Controller;
use Aigisu\Api\Models\Unit\CG;
use Aigisu\Components\Imgur\Imgur;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class ImgurUploader extends Controller
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws NotFoundException
     */
    public function actionDelete(Request $request, Response $response) : Response
    {
        /** @var $cg CG */
        $cg = CG::with('unit')->findOrFail($this->getID($request));
        if (!$id = $cg->getAttribute('imgur_id')) {
            throw new NotFoundException($request, $response);
        }

        $imgur = $this->getImgurManager();
        $imgur->deleteImage($id);
        $cg->fill([
            'imgur_id' => null,
            'imgur_delhash' => null
        ])->saveOrFail();

        return $response->withStatus(self::STATUS_OK);
    }

    /**
     * @return Imgur
     */
    private function getImgurManager() : Imgur
    {
        return $this->get(Imgur::class);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @throws RuntimeException
     * @return Response
     */
    public function actionCreate(Request $request, Response $response) : Response
    {
        /** @var $cg CG */
        $cg = CG::with('unit')->findOrFail($this->getID($request));
        if ($cg->getAttribute('imgur_id')) {
            throw new RuntimeException('CG has image on imgur, at first try to delete.');
        }

        $imgur = $this->getImgurManager();
        $imgurFile = $this->uploadImage($cg, $imgur);
        $cg->fill([
            'imgur_id' => $imgurFile['id'],
            'imgur_delhash' => $imgurFile['deletehash'],
        ])->saveOrFail();

        return $response->withStatus(self::STATUS_OK);
    }

    /**
     * @param CG $cg
     * @param Imgur $imgur
     * @return array
     */
    private function uploadImage(CG $cg, Imgur $imgur) : array
    {
        if ($cg->server == CG::SERVER_DMM) {
            $response = $imgur->uploadDmmImage($this->getImageFileName($cg), [
                'title' => $this->generateName($cg),
                'description' => $this->generateDescription(),
            ]);
        } elseif ($cg->server == CG::SERVER_NUTAKU) {
            $response = $imgur->uploadNutakuImage($this->getImageFileName($cg), [
                'title' => $this->generateName($cg),
                'description' => $this->generateDescription(),
            ]);
        } else {
            throw new RuntimeException('Wrong name of server');
        }

        return $this->responseToDataArray($response);
    }

    /**
     * @param CG $cg
     * @throws RuntimeException
     * @return string
     */
    private function getImageFileName(CG $cg) : string
    {
        if (!file_exists($filename = $this->get('public') . '/' . $cg->getOriginal('local'))) {
            throw new RuntimeException("File {$filename} doesn't exist");
        }

        return $filename;
    }

    /**
     * @param CG $cg
     * @return string
     */
    private function generateName(CG $cg) : string
    {
        $name = "{$cg->server}: {$cg->unit->name} scene: {$cg->scene}";
        if ($cg->archival) {
            $name .= ' Archival';
        }

        return $name;
    }

    /**
     * @return string
     */
    private function generateDescription() : string
    {
        return 'R18';
    }

    /**
     * @param ResponseInterface $response
     * @return array
     */
    private function responseToDataArray(ResponseInterface $response) : array
    {
        $json = (string) $response->getBody();
        $jsonArray = json_decode($json, true);
        return $jsonArray['data'];
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws NotFoundException
     */
    public function actionUpdate(Request $request, Response $response) : Response
    {

        /** @var $cg CG */
        $cg = CG::with('unit')->findOrFail($this->getID($request));
        if (!$id = $cg->getAttribute('imgur_id')) {
            throw new NotFoundException($request, $response);
        }

        $imgur = $this->getImgurManager();
        $imgurFile = $this->uploadImage($cg, $imgur);
        $cg->fill([
            'imgur_id' => $imgurFile['id'],
            'imgur_delhash' => $imgurFile['deletehash'],
        ])->saveOrFail();
        $imgur->deleteImage($id);

        return $response->withStatus(self::STATUS_OK);
    }
}