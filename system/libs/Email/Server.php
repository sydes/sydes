<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes\Email;

class Server
{
    public function send(Message $message) {
        $to = '';
        foreach ($message->getTo() as $toEmail => $toName) {
            if(!empty($toName)){
                $toName = sprintf("=?utf-8?B?%s?= ", base64_encode($toName));
            }
            $to .= $toName . "<" . $toEmail . ">, ";
        }

        $subject = sprintf("=?utf-8?B?%s?= ", base64_encode($message->getSubject()));

        $sent = mail(
            $to,
            $subject,
            $message->getEncodedBody(),
            $message->headersToString()
        );

        if (!$sent) {
            throw new \Exception('The message could not be delivered using mail().');
        }

        return $sent;
    }
}
