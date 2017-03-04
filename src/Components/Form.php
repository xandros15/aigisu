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
        parent::__construct(array_merge($request->getParams(), ['errors' => $request->getAttribute('errors', [])]));
    }

    /**
     * @param Request $request
     * @return Form
     */
    public function withRequest(Request $request)
    {
        $clone = new static($request);
        return $clone;
    }
}
