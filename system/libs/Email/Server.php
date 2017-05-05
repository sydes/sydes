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
        $sent = mail(
            $message->getHeader('To'),
            $message->getSubject(),
            $message->toString(),
            $message->headersToString(),
            '-f '.$message->getHeader('Return-Path')
        );

        if (!$sent) {
            throw new \Exception('The message could not be delivered using mail().');
        }

        return $sent;
    }
}
