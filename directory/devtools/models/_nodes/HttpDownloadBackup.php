<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\models\_nodes;

use DecodeLabs\Exceptional;
use DecodeLabs\Genesis;
use DecodeLabs\R7\Legacy;
use df\arch;

class HttpDownloadBackup extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function execute()
    {
        $fileName = basename($this->request['backup']);

        if (!preg_match('/-([0-9]{14})\.sql/i', $fileName)) {
            throw Exceptional::Forbidden([
                'message' => 'Not an axis backup file',
                'http' => 403
            ]);
        }

        $file = Genesis::$hub->getSharedDataPath() . '/backup/' . $fileName;

        if (!is_file($file)) {
            throw Exceptional::NotFound([
                'message' => 'Backup not found',
                'http' => 404
            ]);
        }

        return Legacy::$http->fileResponse($file)
            ->setContentType('application/sql');
    }
}
