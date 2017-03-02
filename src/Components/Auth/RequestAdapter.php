<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-02
 * Time: 01:29
 */

namespace Aigisu\Components\Auth;


use Slim\Collection;
use Slim\Http\Request;

class RequestAdapter extends Collection
{

    /**
     * TwigAdapter constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct([
            'is_guest' => $request->getAttribute('is_guest', true),
            'user' => $request->getAttribute('user', []),
        ]);
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return $this->all();
    }
}
