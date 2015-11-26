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
            return $this->throwError(403, 'Export is currently only supported with local media libraries', [
                'handler' => $handler->getDisplayName()
            ]);
        }

        try {
            $filePath = $handler->getFilePath(
                $this->request['file'],
                $this->request['version']
            );
        } catch(\Exception $e) {
            return $this->throwError(404, 'Invalid version ids', [
                'fileId' => $this->request['file'],
                'versionId' => $this->request['version']
            ]);
        }

        if(!is_file($filePath)) {
            return $this->throwError(404, 'File not found', [
                'filePath' => $filePath,
                'fileId' => $this->request['file'],
                'versionId' => $this->request['version']
            ]);
        }

        return $this->http->fileResponse($filePath);
    }
}