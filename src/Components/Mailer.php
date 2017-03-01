<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-28
 * Time: 23:46
 */

namespace Aigisu\Components;


class Mailer
{

    const CONTENT_TYPE = 'text/html';
    const CHARSET = 'UTF-8';

    private $defaults = [
        'view' => 'mail.twig',
        'subject' => 'Mailer Notice',
        'charset' => self::CHARSET,
    ];

    /** @var \Twig_Environment */
    private $twig;
    /** @var \Swift_Mailer */
    private $mailer;
    /** @var string|array */
    private $from;
    /** @var string */
    private $prefix;

    /**
     * Mailer constructor.
     * @param \Swift_Mailer $mailer
     * @param \Twig_Environment $twig
     * @param array $params
     */
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, array $params)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->from = $params['from'];
        $this->prefix = $params['prefix'];
    }

    /**
     * @param array $params
     * @return int
     */
    public function send(array $params) : int
    {
        $params = array_merge($this->defaults, $params);
        $mail = $this->createMail($params);
        $mail->setFrom($this->from);
        $mail->setTo($params['to']);
        return $this->mailer->send($mail);
    }

    /**
     * @param $params
     * @return \Swift_Message
     */
    private function createMail($params) : \Swift_Message
    {
        $params['subject'] = trim($this->prefix . ' - ' . $params['subject'], '- ');
        $body = $this->twig->render($params['view'], $params);
        $mail = new \Swift_Message($params['subject'], $body, self::CONTENT_TYPE, $params['charset']);
        return $mail;
    }
}
