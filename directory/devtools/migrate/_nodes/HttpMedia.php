<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\migrate\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

use DecodeLabs\Exceptional;
use DecodeLabs\R7\Legacy;

class HttpMedia extends arch\node\RestApi
{
    public function executeGet()
    {
        $handler = $this->data->media->getMediaHandler();

        if (!$this->data->media->isLocalDataMediaHandler()) {
            throw Exceptional::{'Api,Forbidden'}([
                'message' => 'Export is currently only supported with local media libraries',
                'http' => 403,
                'data' => $handler->getDisplayName()
            ]);
        }

        try {
            $filePath = $handler->getFilePath(
                $this->request['file'],
                $this->request['version']
            );
        } catch (\Throwable $e) {
            throw Exceptional::{'NotFound,InvalidArgument'}([
                'message' => 'Invalid version ids',
                'http' => 404,
                'data' => [
                    'fileId' => $this->request['file'],
                    'versionId' => $this->request['version']
                ]
            ]);
        }

        if (!is_file($filePath)) {
            throw Exceptional::NotFound([
                'message' => 'File not found',
                'http' => 404,
                'data' => [
                    'filePath' => $filePath,
                    'fileId' => $this->request['file'],
                    'versionId' => $this->request['version']
                ]
            ]);
        }

        $output = $this->http->fileResponse($filePath);
        $output->getHeaders()->set('content-length', filesize($filePath));
        return $output;
    }

    public function authorizeRequest()
    {
        $key = $this->data->hexHash(Legacy::getPassKey());

        if ($key != $this->request['key']) {
            throw Exceptional::{'Forbidden,UnexpectedValue'}([
                'message' => 'Pass key is invalid',
                'http' => 403
            ]);
        }
    }
}
