<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-05
 * Time: 22:50
 */

namespace Aigisu\Helpers;


abstract class Filesystem
{
    public static function resolvePath(string $path) : string
    {
        if (!$newPath = realpath($path)) {
            $umask = umask(0);
            if (!@mkdir($path, 0755, true)) {
                throw new \Exception(sprintf('Impossible to create the root directory "%s".', $path));
            }
            umask($umask);
            $newPath = realpath($path);
        }

        return $newPath;
    }
}