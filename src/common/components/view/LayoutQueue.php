<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-09
 * Time: 16:18
 */

namespace Aigisu\Common\Components\View;


use SplPriorityQueue;

class LayoutQueue
{
    /** @var SplPriorityQueue */
    private $beginBody;
    /** @var SplPriorityQueue */
    private $endBody;
    /** @var SplPriorityQueue */
    private $head;

    public function __construct()
    {
        $this->beginBody = new SplPriorityQueue();
        $this->endBody = new SplPriorityQueue();
        $this->head = new SplPriorityQueue();
    }

    /**
     * @return SplPriorityQueue
     */
    public function getBeginBody()
    {
        return $this->beginBody;
    }

    /**
     * @return SplPriorityQueue
     */
    public function getEndBody()
    {
        return $this->endBody;
    }

    /**
     * @return SplPriorityQueue
     */
    public function getHead()
    {
        return $this->head;
    }
}