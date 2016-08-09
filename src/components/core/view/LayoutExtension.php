<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-08
 * Time: 18:05
 */

namespace Aigisu\view;


use SplPriorityQueue;

class LayoutExtension implements ViewExtension
{

    const PH_HEAD = '<![CDATA[SLIM-BLOCK-HEAD]]>';

    const PH_BODY_BEGIN = '<![CDATA[SLIM-BLOCK-BODY-BEGIN]]>';

    const PH_BODY_END = '<![CDATA[SLIM-BLOCK-BODY-END]]>';

    /** @var  LayoutQueue */
    private $queue;

    public function __construct()
    {
        $this->queue = new LayoutQueue();
    }

    public function head()
    {
        echo self::PH_HEAD;
    }


    public function beginPage()
    {
        ob_start();
        ob_implicit_flush(false);
    }

    public function beginBody()
    {
        echo self::PH_BODY_BEGIN;
    }

    public function endBody()
    {
        echo self::PH_BODY_END;
    }

    public function endPage()
    {
        $content = ob_get_clean();

        echo strtr($content, [
            self::PH_HEAD => $this->render(self::PH_HEAD),
            self::PH_BODY_BEGIN => $this->render(self::PH_BODY_BEGIN),
            self::PH_BODY_END => $this->render(self::PH_BODY_END)
        ]);
    }

    private function render($block)
    {
        $content = '';
        foreach ($this->getQueue($block) as $item) {
            $content .= (string) $item() . "\n";
        }

        return $content;
    }

    private function getQueue($block) : SplPriorityQueue
    {
        switch ($block) {
            case self::PH_HEAD:
                $queue = $this->queue->getHead();
                break;
            case self::PH_BODY_BEGIN:
                $queue = $this->queue->getBeginBody();
                break;
            case self::PH_BODY_END:
                $queue = $this->queue->getEndBody();
                break;
            default:
                throw new \InvalidArgumentException("Wrong name of block. You should use class constants");
        }

        return $queue;
    }

    public function applyCallbacks(CallbackManager &$callbackManager)
    {
        $callbackManager->addCallbacks([
            'beginPage' => [$this, 'beginPage'],
            'head' => [$this, 'head'],
            'beginBody' => [$this, 'beginBody'],
            'endBody' => [$this, 'endBody'],
            'endPage' => [$this, 'endPage'],
            'append' => [$this, 'append']
        ]);
    }

    public function append(\Closure $callback, $block, $priority = 10)
    {
        $this->getQueue($block)->insert($callback, $priority);
    }
}