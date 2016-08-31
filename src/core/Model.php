<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-07-11
 * Time: 02:03
 */

namespace Aigisu\Core;

use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * Class Model
 * @package Aigisu
 * @mixin \Eloquent
 */
abstract class Model extends EloquentModel
{
    /**
     * @param array $files
     * @return $this
     */
    public function attachUploadedFiles(array $files)
    {
        foreach ($files as $name => $file) {
            if (method_exists($this, $method = 'attach' . ucfirst($name))) {
                $this->{$method}($file);
            }
        }
    }
}