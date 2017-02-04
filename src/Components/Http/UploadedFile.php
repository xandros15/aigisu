<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-05
 * Time: 17:04
 */

namespace Aigisu\Components\Http;

use League\Flysystem\AdapterInterface;
use League\Flysystem\FilesystemInterface;
use Slim\Http\UploadedFile as SlimUploadedFile;

class UploadedFile extends SlimUploadedFile
{
    /** @var FilesystemInterface|null */
    private $manager;

    /**
     * @param FilesystemInterface $manager
     * @return UploadedFile
     */
    public function withManager(FilesystemInterface $manager)
    {
        $file = clone $this;
        $file->manager = $manager;
        return $file;
    }

    /**
     * Store the uploaded file on a filesystem disk.
     *
     * @param  string $path
     * @param  string $name
     * @return string storage filename
     */
    public function storeAsPublic(string $path, string $name = '') : string
    {
        $this->beforeStore();

        $newName = $this->generateName($path, $name);
        $result = $this->getManager()->putStream($newName, $this->getStream()->detach(), [
            'visibility' => AdapterInterface::VISIBILITY_PUBLIC
        ]);

        if (!$result) {
            throw new RuntimeException("Can't save file {$this->file}");
        }

        $this->afterStore();

        return $newName;
    }

    /**
     * Store the uploaded file on a filesystem disk as private file.
     *
     * @param string $path
     * @param string $name
     * @return string
     */
    public function storeAsPrivate(string $path, string $name = '') : string
    {
        $this->beforeStore();

        $newName = $this->generateName($path, $name);
        $result = $this->getManager()->putStream($newName, $this->getStream()->detach(), [
            'visibility' => AdapterInterface::VISIBILITY_PRIVATE
        ]);

        if (!$result) {
            throw new RuntimeException("Can't save file {$this->file}");
        }

        $this->afterStore();

        return $newName;
    }

    /**
     * @return bool
     */
    public function exist() : bool
    {
        return $this->getError() === UPLOAD_ERR_OK;
    }

    /**
     * Generate new name for file
     *
     * @param string $newPath
     * @param string $newName
     * @return string
     */
    protected function generateName(string $newPath = '', string $newName = '') : string
    {
        if (!$newName) {
            $newName = md5_file($this->file);
        }

        if ($newPath) {
            $newPath = rtrim($newPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }

        return $newPath . $newName;
    }

    /**
     * @return FilesystemInterface
     */
    private function getManager() : FilesystemInterface
    {
        if (!$this->manager) {
            throw new RuntimeException("Missing filesystem manager");
        }

        return $this->manager;
    }

    /**
     * @throws RuntimeException
     */
    private function beforeStore()
    {
        if (!$this->exist()) {
            throw new RuntimeException('The upload fails');
        }

        if ($this->moved) {
            throw new RuntimeException('Uploaded file already moved');
        }
    }

    private function afterStore()
    {
        $this->moved = true;
    }
}
