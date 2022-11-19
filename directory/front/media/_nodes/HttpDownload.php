<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\media\_nodes;

use DecodeLabs\R7\Legacy;

use df\arch;

class HttpDownload extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::ALL;
    public const OPTIMIZE = true;

    public function execute()
    {
        if (isset($this->request['version'])) {
            return $this->media->fetchAndServeVersionDownload(
                $this->request['version']
            );
        }

        $id = $this->request['file'];
        $test = $this->data->media->normalizeFileId($id);

        if ($id != $test) {
            $request = clone $this->request;
            $request->query->file = $test;
            return Legacy::$http->redirect($request)
                ->isPermanent(true);
        }

        return $this->media->fetchAndServeDownload($id, isset($this->request->query->embed));
    }
}
