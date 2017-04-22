<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes\Http;

use Zend\Diactoros\Response;
use Zend\Diactoros\Response\InjectContentTypeTrait;
use Zend\Diactoros\Stream;

class AttachmentResponse extends Response
{
    use InjectContentTypeTrait;

    /**
     * Create a file attachment response.
     *
     * Produces a text response with a Content-Type of given file mime type and a default
     * status of 200.
     *
     * @param string $file    Valid file path
     * @param int    $status  Integer status code for the response; 200 by default.
     * @param array  $headers Array of headers to use at initialization.
     */
    public function __construct($file, $name = null, $status = 200, array $headers = [])
    {
        $fileInfo = new \SplFileInfo($file);

        $headers = array_replace($headers, [
            'content-length'      => $fileInfo->getSize(),
            'content-disposition' => sprintf('attachment; filename=%s', $name ?: $fileInfo->getFilename()),
        ]);

        parent::__construct(
            new Stream($fileInfo->getRealPath(), 'r'),
            $status,
            $this->injectContentType((new \finfo(FILEINFO_MIME_TYPE))->file($fileInfo->getRealPath()), $headers)
        );
    }
}
