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
        if (!is_dir($path)) {
            $umask = umask(0);
            @mkdir($path, 0777, true);
            umask($umask);

            if (!is_dir($path)) {
                throw new \Exception(sprintf('Impossible to create the root directory "%s".', $path));
            }
        }

        return realpath($path);
    }
}