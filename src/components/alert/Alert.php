<?php

namespace Aigisu\Alert;

use Plasticbrain\FlashMessages\FlashMessages;

class Alert
{
    const INFO = FlashMessages::INFO;
    const SUCCESS = FlashMessages::SUCCESS;
    const WARNING = FlashMessages::WARNING;
    const ERROR = FlashMessages::ERROR;

    /** @var FlashMessages */
    private static $flashes;

    public function init()
    {
        (session_id()) || @session_start();
        $flashes = new FlashMessages();
        $flashes->setCloseBtn(self::getCloseButton());
        $flashes->setCssClassMap([
            self::INFO => 'alert-info',
            self::SUCCESS => 'alert-success',
            self::WARNING => 'alert-warning',
            self::ERROR => 'alert-danger',
        ]);
        $flashes->setMsgCssClass(self::getCssClasses());
        $flashes->setMsgWrapper("<div class='%s'>%s</div>");
        static::$flashes = $flashes;
    }

    private static function getCloseButton()
    {
        return <<<HTML
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
</button>
HTML;
    }

    public static function getCssClasses()
    {
        return 'alert fade in';
    }

    public static function add($message, $type = self::SUCCESS)
    {
        static::$flashes->add($message, $type);
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