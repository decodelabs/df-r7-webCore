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

class HttpMedia extends arch\node\RestApi {

    public function executeGet() {
        $handler = $this->data->media->getMediaHandler();

        if(!$this->data->media->isLocalDataMediaHandler()) {
            throw core\Error::{'EApi,EForbidden'}([
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
        } catch(\Throwable $e) {
            throw core\Error::{'ENotFound,EArgument'}([
                'message' => 'Invalid version ids',
                'http' => 404,
                'data' => [
                    'fileId' => $this->request['file'],
                    'versionId' => $this->request['version']
                ]
            ]);
        }

        if(!is_file($filePath)) {
            throw core\Error::{'ENotFound'}([
                'message' => 'File not found',
                'http' => 404,
                'data' => [
                    'filePath' => $filePath,
                    'fileId' => $this->request['file'],
                    'versionId' => $this->request['version']
                ]
            ]);
        }

        return $this->http->fileResponse($filePath);
    }

    public function authorizeRequest() {
        $key = $this->data->hexHash($this->app->getPassKey());

        if($key != $this->request['key']) {
            throw core\Error::{'EForbidden,EValue'}([
                'message' => 'Pass key is invalid',
                'http' => 403
            ]);
        }
    }
}
