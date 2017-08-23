<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-02
 * Time: 18:51
 */

namespace Aigisu\Api\Controllers\Unit\CG;

use Aigisu\Components\Google\GoogleDriveManager;
use Aigisu\Models\Unit\CG;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class GoogleUploader extends AbstractUploader
{
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws NotFoundException
     */
    public function actionDelete(Request $request, Response $response): Response
    {
        /** @var $cg CG */
        $cg = CG::with('unit')->findOrFail($this->getID($request));
        if (!$id = $cg->getAttribute('google_id')) {
            throw new NotFoundException($request, $response);
        }
        $driveManager = $this->getGoogleDriveManager();

        $driveManager->delete($id);
        $cg->setAttribute('google_id', null)->saveOrFail();

        return $this->delete($response);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws FileExistException
     * @return Response
     */
    public function actionCreate(Request $request, Response $response): Response
    {
        /** @var $cg CG */
        $cg = CG::with('unit')->findOrFail($this->getID($request));
        if ($cg->getAttribute('google_id')) {
            throw new FileExistException('CG has image on google, at first try to delete.');
        }

        $driveManager = $this->getGoogleDriveManager();
        $driveFile = $driveManager->create([
            'name' => $this->generateName($cg),
            'filename' => $this->getImageFileName($cg),
        ]);
        $driveManager->anyoneWithLinkCan($driveFile, 'view');

        $cg->setAttribute('google_id', $driveFile->getId())->saveOrFail();

        return $this->create($response, $this->getGoogleLocation($driveFile->getId()));
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws NotFoundException
     */
    public function actionUpdate(Request $request, Response $response): Response
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

        return $this->update($response);
    }

    /**
     * @param string $id
     *
     * @return string
     */
    private function getGoogleLocation(string $id): string
    {
        return sprintf('https://drive.google.com/uc?export=view&id=%s', $id);
    }

    /**
     * @return GoogleDriveManager
     */
    private function getGoogleDriveManager(): GoogleDriveManager
    {
        /** @var $googleSystem GoogleDriveManager */
        $manager = $this->get(GoogleDriveManager::class);
        $manager->getClientManager()->setAccess();

        return $manager;
    }

    /**
     * @param CG $cg
     *
     * @return string
     */
    private function generateName(CG $cg): string
    {
        $name = "{$cg->unit->name} - {$cg->server}{$cg->scene}";
        if ($cg->archival) {
            $name .= ' Archival';
        }

        return $name;
    }
}
