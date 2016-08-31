<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-31
 * Time: 14:27
 */

namespace Aigisu\Api\Models\Events;


use Aigisu\Api\Models\Unit;
use Aigisu\Components\File\FileUploader;
use Aigisu\Core\Model;
use Slim\Http\UploadedFile;

class UnitIconUploadListener implements Event
{
    const SUB_DIRECTORY = 'icons';

    /** @var FileUploader */
    private $uploader;

    /**
     * UnitIconUploadListener constructor.
     * @param FileUploader $uploader
     */
    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * @param Model $unit
     */
    public function __invoke(Model $unit)
    {
        $this->uploadFile($unit);
    }

    /**
     * @param Model $unit
     */
    private function uploadFile(Model $unit)
    {
        if (!$unit instanceof Unit) {
            return;
        }

        if (!$unit->icon instanceof UploadedFile) {
            return;
        }

        $this->uploader->upload($unit->icon, $this->addSubDirectory($unit->icon_name));
    }

    /**
     * @param string $filename
     * @return string
     */
    public function addSubDirectory(string $filename) : string
    {
        return self::SUB_DIRECTORY . DIRECTORY_SEPARATOR . trim($filename, DIRECTORY_SEPARATOR);
    }
}