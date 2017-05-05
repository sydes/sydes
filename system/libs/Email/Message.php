<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes\Email;

class Message extends \Tx\Mailer\Message
{
    /**
     * @return string
     */
    public function getHeader($header, $default = null)
    {
        return isset($this->header[$header]) ? $this->header[$header] : $default;
    }

    public function headersToString()
    {
        $in = '';
        foreach ($this->header as $key => $value) {
            $in .= $key.': '.$value.$this->CRLF;
        }

        return $in;
    }
}
