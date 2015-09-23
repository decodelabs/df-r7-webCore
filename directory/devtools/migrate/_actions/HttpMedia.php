<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\migrate\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpMedia extends arch\restApi\Action {

    public function executeGet() {
        $handler = $this->data->media->getMediaHandler();

        if(!$this->data->media->isLocalDataMediaHandler()) {
            return $this->throwError(403, 'Export is currently only supported with local media libraries', [
                'handler' => $handler->getDisplayName()
            ]);
        }

        try {
            $filePath = $handler->getFilePath(
                $this->request->query['file'],
                $this->request->query['version']
            );
        } catch(\Exception $e) {
            return $this->throwError(404, 'Invalid version ids', [
                'fileId' => $this->request->query['file'],
                'versionId' => $this->request->query['version']
            ]);
        }

        if(!is_file($filePath)) {
            return $this->throwError(404, 'File not found', [
                'filePath' => $filePath,
                'fileId' => $this->request->query['file'],
                'versionId' => $this->request->query['version']
            ]);
        }

        return $this->http->fileResponse($filePath);
    }
}