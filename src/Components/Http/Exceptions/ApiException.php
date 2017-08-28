<?php


namespace Aigisu\Components\Http\Exceptions;


class ApiException extends RuntimeException
{
    /** @var int */
    protected $code;
    /** @var string */
    protected $message;
    /** @var string */
    protected $file;
    /** @var int */
    protected $line;

    public function __construct(array $error)
    {
        parent::__construct();
        $this->code = $error['code'];
        $this->message = $error['message'];
        $this->file = $error['file'];
        $this->line = $error['line'];
    }
}
