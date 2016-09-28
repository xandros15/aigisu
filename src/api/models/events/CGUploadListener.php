<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-28
 * Time: 01:19
 */

namespace Aigisu\Api\Models\Events;


use Aigisu\Api\Models\Unit\CG;
use Aigisu\Components\Http\Filesystem\FilesystemManager;
use Aigisu\Components\Http\UploadedFile;
use Aigisu\Core\Model;
use Slim\Http\Request;

class CGUploadListener implements Event
{
    const FILE_CATALOG = 'cg';
    const FILE_NAME = 'cg';

    /** @var Request */
    private $request;
    /** @var FilesystemManager */
    private $manager;

    /**
     * IconUploadListener constructor.
     * @param Request $request
     * @param FilesystemManager $manager
     */
    public function __construct(Request $request, FilesystemManager $manager)
    {
        $this->request = $request;
        $this->manager = $manager;
    }

    /**
     * @param Model $model
     * @return void
     */
    public function __invoke(Model $model)
    {
        if ($model instanceof CG) {
            $this->uploadCG($model);
        }
    }

    /**
     * @param CG $cg
     */
    public function uploadCG(CG $cg)
    {
        $fileName = UploadedFile::file($this->request, self::FILE_NAME, $this->manager)->store(self::FILE_CATALOG);
        if ($fileName) {
            $cg->local = $fileName;
        }
    }
}