<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\media\_nodes;

use DecodeLabs\R7\Legacy;

use df\arch;

class HttpImage extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute()
    {
        set_time_limit(120);
        $transform = $this->request['transform'];

        if (isset($this->request['version'])) {
            return $this->media->fetchAndServeVersionImage(
                $this->request['version'],
                $transform
            );
        }

        $id = $this->request['file'];
        $test = $this->data->media->normalizeFileId($id, $transform);

        if ($id != $test) {
            $request = clone $this->request;
            $request->query->file = $test;
            $request->query->transform = $transform;

            return Legacy::$http->redirect($request)
                ->isPermanent(true);
        }

        return $this->media->fetchAndServeImage(
            $id,
            $transform,
            isset($this->request->query->embed)
        );
    }
}
