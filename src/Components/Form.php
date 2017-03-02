<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-02
 * Time: 00:20
 */

namespace Aigisu\Components;


use Slim\Collection;
use Slim\Http\Request;

class Form extends Collection
{
    /**
     * Form constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $items = $this->setForm($request);
        parent::__construct($items);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function setForm(Request $request) : array
    {
        return [
            'form' => $request->getParams(),
            'errors' => $request->getAttribute('errors', []),
        ];
    }
}
