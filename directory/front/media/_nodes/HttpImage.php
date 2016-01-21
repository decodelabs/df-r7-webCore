<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\media\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\neon;

class HttpImage extends arch\node\Base {

    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute() {
        $transform = $this->request['transform'];

        if(isset($this->request['version'])) {
            return $this->media->fetchAndServeVersionImage(
                $this->request['version'],
                $transform
            );
        }

        $id = $this->request['file'];
        $test = $this->data->media->normalizeFileId($id, $transform);

        if($id != $test) {
            $request = clone $this->request;
            $request->query->file = $test;
            $request->query->transform = $transform;

            return $this->http->redirect($request)
                ->isPermanent(true);
        }

        return $this->media->fetchAndServeImage(
            $id, $transform, isset($this->request->query->embed)
        );
    }
}