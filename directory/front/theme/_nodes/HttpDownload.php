<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\theme\_nodes;

use df;
use df\core;
use df\arch;
use df\aura;
use df\neon;

class HttpDownload extends arch\node\Base {

    const OPTIMIZE = true;
    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute() {
        $theme = aura\theme\Base::factory($this->request['theme']);
        $assetPath = $this->request['file'];
        $type = null;
        $cacheAge = $this->application->isDevelopment() ? null : '30 minutes';

        $fileName = basename($assetPath);
        $parts = explode('.', $fileName);

        if(array_pop($parts) == 'map') {
            switch(array_pop($parts)) {
                case 'scss':
                case 'sass':
                    $type = 'application/x-sass-map';
                    $assetPath = substr($assetPath, 0, -4);
                    break;
            }
        }

        if(!$absolutePath = $theme->findAsset($assetPath)) {
            $this->throwError(404, 'File not found');
        }

        if(!$type) {
            $type = core\fs\Type::fileToMime($absolutePath);
        }

        if(substr($type, 0, 6) == 'image/' && isset($this->request['transform'])) {
            $cache = neon\raster\Cache::getInstance();
            $absolutePath = $cache->getTransformationFilePath($absolutePath, $this->request['transform']);
        }

        switch($type) {
            case 'text/x-sass':
            case 'text/x-scss':
                $bridge = new aura\css\sass\Bridge($this->context, $absolutePath);
                return $bridge->getHttpResponse();

            case 'application/x-sass-map':
                $bridge = new aura\css\sass\Bridge($this->context, $absolutePath);
                return $bridge->getMapHttpResponse();

            default:
                $output = $this->http->fileResponse($absolutePath);
                $output->setContentType($type);
        }

        $output->setFileName(basename($absolutePath))
            ->getHeaders()
                ->set('Access-Control-Allow-Origin', '*');

        if($cacheAge) {
            $output->getHeaders()->setCacheExpiration($cacheAge);
        }

        return $output;
    }
}