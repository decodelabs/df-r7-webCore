<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\neon;

class HttpApplicationImage extends arch\action\Base {

    const DEFAULT_ACCESS = arch\IAccess::ALL;
    const CHECK_ACCESS = false;

    public function executeAsPng() {
        $theme = $this->apex->getTheme();

        if(!$path = $theme->getApplicationImagePath()) {
            $this->throwError(404, 'No application image path set');
        }

        $absPath = $theme->findAsset($path);
        $type = core\fs\Type::fileToMime($absPath);

        if(!$absPath) {
            $this->throwError(404, 'Application image '.$path.' not found');
        }

        if(isset($this->request['width'])) {
            $width = $this->request['width'];
            $height = $this->request->query->get('height', $width);
            $cache = neon\raster\Cache::getInstance();
            $absPath = $cache->getTransformationFilePath($absPath, '[rs:'.$width.'|'.$height.']');
        }

        return $this->http->fileResponse($absPath)
            ->setContentType($type);
    }
}