<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\neon;

class HttpApplicationImage extends arch\node\Base {

    const DEFAULT_ACCESS = arch\IAccess::ALL;
    const CHECK_ACCESS = false;

    public function executeAsPng() {
        $theme = $this->apex->getTheme();

        if(!$path = $theme->getApplicationImagePath()) {
            throw core\Error::{'core/fs/ENotFound'}([
                'message' => 'No application image path set',
                'http' => 404
            ]);
        }

        $absPath = $theme->findAsset($path);
        $type = core\fs\Type::fileToMime($absPath);

        if(!$absPath) {
            throw core\Error::{'core/fs/ENotFound'}([
                'message' => 'Application image '.$path.' not found',
                'http' => 404
            ]);
        }

        if(isset($this->request['width'])) {
            $width = $this->request['width'];
            $height = $this->request->query->get('height', $width);
            $fileStore = neon\raster\FileStore::getInstance();
            $absPath = $fileStore->getTransformationFilePath($absPath, '[rs:'.$width.'|'.$height.']');
        }

        return $this->http->fileResponse($absPath)
            ->setContentType($type);
    }
}
