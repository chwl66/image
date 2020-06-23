<?php


namespace app\provider;


use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class Email
{


    /**
     * @var Swift_Mailer
     */
    private $mailer;
    /**
     * @var array|bool
     */
    private $config;

    public function __construct()
    {
        $this->config = hidove_config_get('system.email.');
        $transport = (new Swift_SmtpTransport())
            ->setHost($this->config['host'])
            ->setPort( $this->config['port'])
            ->setEncryption($this->config['encryption'])
            ->setUsername($this->config['username'])
            ->setPassword($this->config['password']);

        $this->mailer = new Swift_Mailer($transport);
    }

    /**
     * @param $subject String 标题
     * @param $toAddresses String|array 接收邮箱
     * @param $body String 内容
     * @return bool
     */

    public function run($subject, $toAddresses, $body)
    {
        $message = (new Swift_Message($subject))
            ->setFrom($this->config['username'],$this->config['sender'])
            ->setTo($toAddresses)
            ->setBody($body,'text/html');

        $result = $this->mailer->send($message);
        if ($result > 0) {
            return true;
        }
        return false;
    }

}