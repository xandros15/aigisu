<?php

namespace models;

use models\Image;

class ImageFile extends Image
{
    const MAX_WIDTH    = 961;
    const MAX_HEIGHT   = 641;
    const MIN_WIDTH    = 959;
    const MIN_HEIGHT   = 639;
    const MIN_FILESIZE = 90 * 1024;
    const MAX_FILESIZE = 2 * 1024 * 1024;

    public function rules()
    {
        return array_merge(parent::rules(),
            [
            'file' => ['required'],
            'height' => ['required', 'integer', 'min:'.self::MIN_HEIGHT,'max:'.self::MAX_HEIGHT],
            'width' => ['required', 'integer', 'min:'.self::MIN_WIDTH,'max:'.self::MAX_WIDTH],
            'size' => ['required', 'integer', 'min:' . self::MIN_FILESIZE, 'max:' . self::MAX_FILESIZE],
            'mime' => ['required', 'in:image/png']
        ]);
    }
}