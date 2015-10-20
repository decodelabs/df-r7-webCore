<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\media\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\neon;

class HttpDownload extends arch\Action {

    const DEFAULT_ACCESS = arch\IAccess::ALL;
    const OPTIMIZE = true;

    public function execute() {
        if(isset($this->request['version'])) {
            return $this->media->fetchAndServeVersionDownload(
                $this->request['version']
            );
        }

        $id = $this->request['file'];
        $test = $this->data->media->normalizeFileId($id);

        if($id != $test) {
            $request = clone $this->request;
            $request->query->file = $test;
            return $this->http->redirect($request)
                ->isPermanent(true);
        }

        return $this->media->fetchAndServeDownload($id);
    }
}