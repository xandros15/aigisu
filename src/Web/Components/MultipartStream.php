<?php


namespace Aigisu\Web\Components;


use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

class MultipartStream extends \GuzzleHttp\Psr7\MultipartStream
{
    /**
     * MultipartStream constructor.
     *
     * @param ServerRequestInterface $request
     * @param null $boundary
     */
    public function __construct(ServerRequestInterface $request, $boundary = null)
    {
        $data = [];
        foreach ($this->paramsResolver($request->getParsedBody()) as $name => $value) {
            $data[] = ['name' => $name, 'contents' => $value];
        }
        foreach ($resolve = $this->paramsResolver($request->getUploadedFiles()) as $name => $file) {
            /** @var $file UploadedFileInterface */
            if (!$file->getError()) {
                $data[] = ['name' => $name, 'contents' => $file->getStream()];
            }
        }
        parent::__construct($data, $boundary);
    }

    /**
     * @param array $params
     * @param string $originalName
     *
     * @return array
     */
    private function paramsResolver(array $params, $originalName = '')
    {
        $output = [];

        foreach ($params as $key => $value) {
            $newKey = $originalName;
            $newKey .= !$originalName ? $key : '[' . $key . ']';
            if (is_array($value)) {
                $output = array_merge($output, $this->paramsResolver($value, $newKey));
            } else {
                $output[$newKey] = $value;
            }
        }

        return $output;
    }
}
