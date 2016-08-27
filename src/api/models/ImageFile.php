<?php

namespace Aigisu\Api\Models;


class ImageFile extends Image
{
    const MAX_WIDTH    = 961;
    const MAX_HEIGHT   = 641;
    const MIN_WIDTH    = 959;
    const MIN_HEIGHT   = 639;
    const MIN_FILESIZE = 90 * 1024;
    const MAX_FILESIZE = 2 * 1024 * 1024;
}