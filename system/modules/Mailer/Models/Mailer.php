<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Sydes;

use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    protected $mail;
    protected $config;

    public function __construct(PHPMailer $mailer)
    {
        $this->mail = $mailer;
    }

    public function init(array $config)
    {
        $this->config = $config;

        if ($config['useSmtp']) {
            $this->mail->SMTPDebug = 2;
            $this->mail->isSMTP();
            $this->mail->Host = $config['smtpHost'];
            $this->mail->SMTPAuth = true;
            $this->mail->Username = $config['smtpUser'];
            $this->mail->Password = $config['smtpPass'];
            $this->mail->Port = $config['smtpPort'];

            if ($config['smtpSecure']) {
                $this->mail->SMTPSecure = 'tls';
            }
        }

        return $this;
    }

    public function send(array $message)
    {
        $message = array_merge([
            'from'    => $this->config['defaultFrom'],
            'to'      => $this->config['defaultTo'],
            'bcc'     => '',
            'subject' => 'No subject',
            'body'    => 'No message',
        ], $message);

        if (!empty($message['from'])) {
            $this->mail->setFrom($message['from']);
        }

        if (empty($message['to'])) {
            throw new \Exception('No recipient for this email');
        }

        $recipients = explode(',', $message['to']);
        foreach ($recipients as $to) {
            $this->mail->addAddress(trim($to));
        }

        if (!empty($message['replyTo'])) {
            $this->mail->addReplyTo($message['replyTo']);
        }

        if ($message['cc']) {
            $recipients = explode(',', $message['cc']);
            foreach ($recipients as $to) {
                $this->mail->addCC(trim($to));
            }
        }

        if (!empty($this->config['sendAlso'])) {
            $message['bcc'] .= $message['bcc'] ? ','.$this->config['sendAlso'] : $this->config['sendAlso'];
        }

        if ($message['bcc']) {
            $recipients = explode(',', $message['bcc']);
            foreach ($recipients as $to) {
                $this->mail->addBCC('', trim($to));
            }
        }

        $this->mail->Subject = $message['subject'];
        $this->mail->Body = $message['body'];

        if ($message['messageType'] == 'html') {
            $this->mail->isHTML(true);
            if (!empty($message['altBody'])) {
                $this->mail->AltBody = $message['altBody'];
            }
        }

        if (isset($message['attachments'])) {
            foreach ($message['attachments'] as $attachment) {
                $this->mail->addAttachment($attachment['file'], $attachment['name']);
            }
        }

        $this->mail->send();

        return $this;
    }
}
