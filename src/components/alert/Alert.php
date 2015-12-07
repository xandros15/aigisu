<?php

namespace app\alert;

use Plasticbrain\FlashMessages\FlashMessages;

class Alert
{
    const CSS_CLASS = 'alert';
    const INFO      = 'i';
    const SUCCESS   = 's';
    const WARNING   = 'w';
    const ERROR     = 'e';

    /** @var FlashMessages */
    private static $flashes;

    public function init()
    {
        (session_id()) || @session_start();
        $flashes         = new FlashMessages();
        $flashes->setCloseBtn('<button type="button" class="close"
                        data-dismiss="alert"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>');
        $flashes->setCssClassMap([
            FlashMessages::INFO => 'alert-info',
            FlashMessages::SUCCESS => 'alert-success',
            FlashMessages::WARNING => 'alert-warning',
            FlashMessages::ERROR => 'alert-danger',
        ]);
        $flashes->setMsgCssClass(self::CSS_CLASS);
        $flashes->setMsgWrapper("<div class='%s'>%s</div>");
        static::$flashes = $flashes;
    }

    public static function add($message, $type = self::SUCCESS)
    {
        switch ($type) {
            case self::ERROR:
                static::$flashes->error($message);
                break;
            case self::INFO:
                static::$flashes->info($message);
                break;
            case self::SUCCESS:
                static::$flashes->success($message);
                break;
            case self::WARNING:
                static::$flashes->warning($message);
                break;
        }
    }

    public static function hasErrors()
    {
        return static::$flashes->hasErrors();
    }

    public static function hasMessages()
    {
        return static::$flashes->hasMessages();
    }

    public static function display()
    {
        return static::$flashes->display();
    }
}