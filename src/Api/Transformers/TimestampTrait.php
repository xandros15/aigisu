<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-06
 * Time: 22:39
 */

namespace Aigisu\Api\Transformers;


trait TimestampTrait
{

    /**
     * @param \DateTimeInterface $dateTime
     * @param string $format
     * @return string
     */
    protected function createTimestamp(\DateTimeInterface $dateTime, string $format = 'Y-m-d H:i:s') : string
    {
        return $dateTime->format($format);
    }
}