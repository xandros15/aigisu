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
use Psr\Http\Message\ServerRequestInterface;
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
     * @param ServerRequestInterface $request
     * @param string $key
     * @param null $manager
     * @return UploadedFile
     */
    public static function file(ServerRequestInterface $request, string $key, $manager = null)
    {
        $target = $request->getUploadedFiles();
        $key = explode('.', $key);

        while (($segment = array_shift($key)) !== null) {
            if (array_key_exists($segment, $target)) {
                $target = $target[$segment];
            } else {
                $target = null;
                break;
            }
        }

        return is_null($target) ? self::createFakeFile() : self::createFromBase($target, $manager);
    }

    /**
     * @return UploadedFile
     */
    public static function createFakeFile() : UploadedFile
    {
        return new static('', null, null, null, UPLOAD_ERR_NO_FILE);
    }

    /**
     *
     * Create a new file instance from a base instance.
     *
     * @param SlimUploadedFile $file
     * @param FilesystemManager|null $manager
     * @return UploadedFile
     */
    public static function createFromBase(SlimUploadedFile $file, $manager = null) : UploadedFile
    {
        if (!$file instanceof static) {
            $file = new static($file->file, $file->name, $file->type, $file->size, $file->error, $file->sapi);
        }

        $file->setManager($manager);

        return $file;
    }

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
        if ($this->exist()) {
            return $this->storeAs($path, $this->hashName(), $disk, self::VISIBILITY_PUBLIC);
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
     * Store the uploaded file on a filesystem disk.
     *
     * @param  string $path
     * @param  string $name
     * @param  string $disk
     * @param  string $visibility
     * @return string|false
     */
    public function storeAs(string $path, string $name, string $disk = '', string $visibility = self::VISIBILITY_PUBLIC)
    {
        if ($this->exist() && $this->manager instanceof FilesystemManager) {
            $path = trim($path . DIRECTORY_SEPARATOR . $name, DIRECTORY_SEPARATOR);
            $result = $this->manager->disk($disk)->putStream($path, $this->getStream()->detach(), [
                'visibility' => $this->prepareVisibility($visibility)
            ]);

            return $result ? $path : false;
        }

        return false;
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
     * Get a filename for the file that is the MD5 hash of the contents.
     *
     * @param  string $path
     * @return string
     */
    public function hashName(string $path = '')
    {
        if ($path) {
            $path = rtrim($path, '/') . '/';
        }
        $path .= md5_file($this->file);

        return $path;
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
        if ($this->exist()) {
            return $this->storeAs($path, $this->hashName(), $disk);
        }
        return false;
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
        if ($this->exist()) {
            return $this->storeAs($path, $name, $disk, self::VISIBILITY_PUBLIC);
        }
        return false;
    }
}