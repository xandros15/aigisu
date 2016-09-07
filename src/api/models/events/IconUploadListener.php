<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-06
 * Time: 14:12
 */

namespace Aigisu\Api\Models\Events;


use Aigisu\Api\Models\Unit;
use Aigisu\Components\Http\Filesystem\FilesystemManager;
use Aigisu\Components\Http\UploadedFile;
use Aigisu\Core\Model;
use Slim\Http\Request;

class IconUploadListener implements Event
{

    const FILE_CATALOG = 'icons';
    const FILE_NAME = 'icon';

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
        if ($model instanceof Unit) {
            $this->uploadIcon($model);
        }
    }

    /**
     * @param Unit $unit
     */
    private function uploadIcon(Unit $unit)
    {
        $iconName = UploadedFile::file($this->request, self::FILE_NAME, $this->manager)->store(self::FILE_CATALOG);
        if ($iconName) {
            $unit->icon = $iconName;
        }
    }
}