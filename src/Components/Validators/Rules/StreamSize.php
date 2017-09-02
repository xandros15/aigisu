<?php


namespace Aigisu\Components\Validators\Rules;


use Psr\Http\Message\StreamInterface;
use Respect\Validation\Rules\Size;

final class StreamSize extends Size
{
    /**
     * @param $input
     *
     * @return bool
     */
    public function validate($input)
    {
        if ($input instanceof StreamInterface) {
            return $this->isValidSize($input->getSize());
        }

        return parent::validate($input);
    }

    /**
     * @param int $size
     *
     * @return bool
     */
    private function isValidSize($size)
    {
        if (null !== $this->minValue && null !== $this->maxValue) {
            return ($size >= $this->minValue && $size <= $this->maxValue);
        }

        if (null !== $this->minValue) {
            return ($size >= $this->minValue);
        }

        return ($size <= $this->maxValue);
    }
}
