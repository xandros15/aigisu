<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-02
 * Time: 18:51
 */

namespace Aigisu\Api\Controllers\Unit\CG;

use Aigisu\Api\Models\Unit\CG;
use Aigisu\Components\Google\GoogleDriveFilesystem;
use Aigisu\components\google\GoogleDriveManager;
use RuntimeException;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class GoogleUploader extends AbstractUploader
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
        if (!$id = $cg->getAttribute('google_id')) {
            throw new NotFoundException($request, $response);
        }
        $driveManager = $this->getGoogleDriveManager();

        $driveManager->delete($id);
        $cg->setAttribute('google_id', null)->saveOrFail();

        return $response->withStatus(self::STATUS_OK);
    }

    /**
     * @return GoogleDriveManager
     */
    private function getGoogleDriveManager() : GoogleDriveManager
    {
        $googleSystem = $this->get(GoogleDriveFilesystem::class);
        $googleSystem->getClientManager()->getAccess();
        return $googleSystem->getDriveManager();
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
        if ($cg->getAttribute('google_id')) {
            throw new RuntimeException('CG has image on google, at first try to delete.');
        }

        $driveManager = $this->getGoogleDriveManager();
        $driveFile = $driveManager->create([
            'name' => $this->generateName($cg),
            'filename' => $this->getImageFileName($cg)
        ]);
        $driveManager->anyoneWithLinkCan($driveFile, 'view');

        $cg->setAttribute('google_id', $driveFile->getId())->saveOrFail();

        return $response->withStatus(self::STATUS_CREATED)->withHeader('Location', $this->getLocation($request));
    }

    /**
     * @param CG $cg
     * @return string
     */
    private function generateName(CG $cg) : string
    {
        $name = "{$cg->unit->name} - {$cg->server}{$cg->scene}";
        if ($cg->archival) {
            $name .= ' Archival';
        }

        return $name;
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
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws NotFoundException
     */
    public function actionUpdate(Request $request, Response $response) : Response
    {
        /** @var $cg CG */
        $cg = CG::with('unit')->findOrFail($this->getID($request));
        if (!$id = $cg->getAttribute('google_id')) {
            throw new NotFoundException($request, $response);
        }

        $driveManager = $this->getGoogleDriveManager();
        $driveManager->update($id, [
            'name' => $this->generateName($cg),
            'filename' => $this->getImageFileName($cg),
        ]);

        return $response->withStatus(self::STATUS_OK);
    }
}
