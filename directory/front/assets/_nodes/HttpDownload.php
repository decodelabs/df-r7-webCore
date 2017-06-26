<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\assets\_nodes;

use df;
use df\core;
use df\arch;
use df\aura;
use df\neon;

class HttpDownload extends arch\node\Base {

    const OPTIMIZE = true;
    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute() {
        $path = core\uri\Path::normalizeLocal($this->request['file']);

        if(!$absolutePath = df\Launchpad::$loader->findFile('apex/assets/'.$path)) {
            throw core\Error::{'core/fs/ENotFound'}([
                'message' => 'File not found',
                'http' => 404
            ]);
        }

        $type = null;
        $fileName = basename($absolutePath);

        $hasTransform = isset($this->request['transform']);
        $hasFavicon = isset($this->request['favicon']);

        if($hasTransform || $hasFavicon) {
            $type = core\fs\Type::fileToMime($absolutePath);

            if(substr($type, 0, 6) == 'image/') {
                $fileStore = neon\raster\FileStore::getInstance();

                if($hasTransform) {
                    $absolutePath = $fileStore->getTransformationFilePath($absolutePath, $this->request['transform']);
                }

                if($type != 'image/x-icon' && $hasFavicon) {
                    if(preg_match('/MSIE ([0-9]{1,}[\.0-9]{0,})/', $this->http->getUserAgent())) {
                        $absolutePath = $fileStore->getIconFilePath($absolutePath, 16, 32);
                        $type = 'image/x-icon';
                        $fileName .= '.ico';
                    }
                }
            }
        }



        $output = $this->http->fileResponse($absolutePath);

        if($type) {
            $output->setContentType($type);
        }

        $output->setFileName($fileName, isset($this->request['attachment']))
            ->getHeaders()
                ->set('Access-Control-Allow-Origin', '*');

        return $output;
    }
}
