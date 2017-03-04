<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-28
 * Time: 20:33
 */

namespace Aigisu\Components;


use Slim\Flash\Messages;

class Flash
{
    const KEY_NAME = 'flash';
    /** @var Messages */
    private $messages;

    /**
     * Flash constructor.
     * @param Messages $messages
     */
    public function __construct(Messages $messages)
    {
        $this->messages = $messages;
    }

    /**
     * @param string $message
     * @param bool $now
     */
    public function addError(string $message, bool $now = false): void
    {
        $this->addFormattedMessage(['type' => 'error', 'value' => $message], $now);
    }

    /**
     * @param string $message
     * @param bool $now
     */
    public function addWarning(string $message, bool $now = false): void
    {
        $this->addFormattedMessage(['type' => 'warning', 'value' => $message], $now);
    }

    /**
     * @param string $message
     * @param bool $now
     */
    public function addSuccess(string $message, bool $now = false): void
    {
        $this->addFormattedMessage(['type' => 'success', 'value' => $message], $now);
    }

    /**
     * @param array $message
     * @param bool $now
     */
    private function addFormattedMessage(array $message, bool $now): void
    {
        if ($now) {
            $this->messages->addMessageNow(self::KEY_NAME, $message);
        } else {
            $this->messages->addMessage(self::KEY_NAME, $message);
        }
    }
}
