<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes\Email;

use Tx\Mailer\SMTP;

class Sender
{
    /** @var Server */
    protected $server;
    protected $config;

    public function init(array $config)
    {
        $this->config = $config;

        if ($config['useSmtp']) {
            $this->server = (new SMTP)
                ->setServer($config['smtpHost'], $config['smtpPort'], $config['smtpSecure'])
                ->setAuth($config['smtpUser'], $config['smtpPass']);
        } else {
            $this->server = new Server;
        }

        return $this;
    }

    public function send(array $message)
    {
        $message = array_merge([
            'from' => $this->config['defaultFrom'],
            'to' => $this->config['defaultTo'],
            'replyTo' => '',
            'cc' => '',
            'bcc' => '',
            'subject' => 'No subject',
            'body' => '',
        ], $message);

        if (empty($message['to'])) {
            throw new \Exception('No recipient for this email');
        }

        $mail = new Message;
        $mail->setFrom('', $message['from']);

        $recipients = explode(',', $message['to']);
        foreach ($recipients as $to) {
            $mail->addTo('', trim($to));
        }

        $mail->setSubject($message['subject'])
            ->setBody($message['body']);

        if ($message['replyTo']) {
            $mail->setReplyTo('', $message['replyTo']);
        }

        if ($message['cc']) {
            $recipients = explode(',', $message['cc']);
            foreach ($recipients as $to) {
                $mail->addCc('', trim($to));
            }
        }

        if ($message['bcc']) {
            $message['bcc'] .= ','.$this->config['sendAlso'];
        } else {
            $message['bcc'] = $this->config['sendAlso'];
        }

        if ($message['bcc']) {
            $recipients = explode(',', $message['bcc']);
            foreach ($recipients as $to) {
                $mail->addBcc('', trim($to));
            }
        }

        if (isset($message['attachments'])) {
            foreach ($message['attachments'] as $attachment) {
                $mail->addAttachment($attachment['name'], $attachment['file']);
            }
        }

        $this->server->send($mail);

        return $this;
    }
}
