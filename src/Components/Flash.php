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
     *
     * @param Messages $messages
     */
    public function __construct(Messages $messages)
    {
        $this->messages = $messages;
    }

    /**
     * @param string $message
     */
    public function addError(string $message): void
    {
        $this->addFormattedMessage(['type' => 'error', 'value' => $message]);
    }

    /**
     * @param string $message
     */
    public function addWarning(string $message): void
    {
        $this->addFormattedMessage(['type' => 'warning', 'value' => $message]);
    }

    /**
     * @param string $message
     */
    public function addSuccess(string $message): void
    {
        $this->addFormattedMessage(['type' => 'success', 'value' => $message]);
    }

    /**
     * @param string $message
     */
    public function addInstantError(string $message): void
    {
        $this->addInstantFormattedMessage(['type' => 'error', 'value' => $message]);
    }

    /**
     * @param string $message
     */
    public function addInstantWarning(string $message): void
    {
        $this->addInstantFormattedMessage(['type' => 'warning', 'value' => $message]);
    }

    /**
     * @param string $message
     */
    public function addInstantSuccess(string $message): void
    {
        $this->addInstantFormattedMessage(['type' => 'success', 'value' => $message]);
    }

    /**
     * @param array $message
     */
    private function addInstantFormattedMessage(array $message): void
    {
        $this->messages->addMessageNow(self::KEY_NAME, $message);
    }

    /**
     * @param array $message
     */
    private function addFormattedMessage(array $message): void
    {
        $this->messages->addMessage(self::KEY_NAME, $message);
    }
}
