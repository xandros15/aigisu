<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-05
 * Time: 17:04
 */

namespace Aigisu\Components\Http;

use Aigisu\Components\Http\Filesystem\FilesystemManager;
use League\Flysystem\AdapterInterface;
use Slim\Http\UploadedFile as SlimUploadedFile;

class UploadedFile extends SlimUploadedFile
{
    /** @const VISIBILITY_PUBLIC public visibility */
    const VISIBILITY_PUBLIC = 'public';

    /** @const VISIBILITY_PRIVATE private visibility */
    const VISIBILITY_PRIVATE = 'private';

    /** @var FilesystemManager|null */
    protected $manager;

    /**
     * @param null|FilesystemManager $manager
     */
    public function setManager($manager)
    {
        if ($manager instanceof FilesystemManager) {
            $this->manager = $manager;
        }
    }

    /**
     * Store the uploaded file on a filesystem disk with public visibility.
     *
     * @param  string $path
     * @param  string $disk
     * @return string|false
     */
    public function storePublicly(string $path, string $disk = '')
    {
        return $this->storeAs($path, '', $disk, self::VISIBILITY_PUBLIC);
    }

    /**
     * Store the uploaded file on a filesystem disk.
     *
     * @param  string $path
     * @param  string $name
     * @param  string $disk
     * @param  string $visibility
     * @return string|false
     */
    public function storeAs(
        string $path,
        string $name = '',
        string $disk = '',
        string $visibility = self::VISIBILITY_PUBLIC
    ) {
        if ($this->exist() && $this->manager instanceof FilesystemManager) {
            $path = $name ? $path . DIRECTORY_SEPARATOR . $name : $this->hashName($path);
            $result = $this->manager->disk($disk)->putStream($path, $this->getStream()->detach(), [
                'visibility' => $this->prepareVisibility($visibility)
            ]);

            return $result ? $path : false;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function exist() : bool
    {
        return $this->getError() === UPLOAD_ERR_OK;
    }

    /**
     * Get a filename for the file that is the MD5 hash of the contents.
     *
     * @param  string $path
     * @return string
     */
    public function hashName(string $path = '')
    {
        if ($path) {
            $path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }
        $path .= md5_file($this->file);

        return $path;
    }

    /**
     * @param string $value
     * @return string
     */
    private function prepareVisibility(string $value) : string
    {
        switch ($value) {
            case self::VISIBILITY_PRIVATE:
                return AdapterInterface::VISIBILITY_PRIVATE;
            case self::VISIBILITY_PUBLIC:
            default:
                return AdapterInterface::VISIBILITY_PUBLIC;
        }
    }

    /**
     * Store the uploaded file on a filesystem disk.
     *
     * @param  string $path
     * @param  string $disk
     * @return string|false
     */
    public function store(string $path, string $disk = '')
    {
        return $this->storeAs($path, '', $disk);
    }

    /**
     * Store the uploaded file on a filesystem disk with public visibility.
     *
     * @param  string $path
     * @param  string $name
     * @param  string $disk
     * @return string|false
     */
    public function storePubliclyAs(string $path, string $name, string $disk = '')
    {
        return $this->storeAs($path, $name, $disk, self::VISIBILITY_PUBLIC);
    }
}
