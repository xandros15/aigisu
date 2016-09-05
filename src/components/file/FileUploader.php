<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-31
 * Time: 13:38
 */

namespace Aigisu\Components\File;


use Psr\Http\Message\StreamInterface;

class FileUploader
{
    /** @var string */
    protected $targetDir;

    /**
     * FileUploader constructor.
     * @param string $targetDir
     */
    public function __construct(string $targetDir)
    {
        $this->targetDir = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * @param StreamInterface $stream
     * @param string $target
     * @return bool
     */
    public function upload(StreamInterface $stream, string $target)
    {
        return $this->writeStream($stream, $this->targetDir . $target);
    }

    /**
     * @inheritdoc
     */
    public function writeStream(StreamInterface $source, string $location)
    {
        $response = false;
        $this->ensureDirectory(dirname($location));

        if ($target = @fopen($location, 'w+b')) {
            $source->rewind();
            stream_copy_to_stream($source->detach(), $target);
            $response = fclose($target);
        }

        return $response;
    }

    /**
     * Ensure the root directory exists.
     *
     * @param string $root root directory path
     *
     * @return string real path to root
     *
     * @throws \Exception in case the root directory can not be created
     */
    protected function ensureDirectory($root)
    {
        if (!is_dir($root)) {
            $umask = umask(0);
            @mkdir($root, 0777, true);
            umask($umask);

            if (!is_dir($root)) {
                throw new \Exception(sprintf('Impossible to create the root directory "%s".', $root));
            }
        }

        return realpath($root);
    }
}