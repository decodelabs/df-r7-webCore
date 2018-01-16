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

        $fileName = basename($absolutePath);
        $type = core\fs\Type::fileToMime($absolutePath);

        $hasTransform = isset($this->request['transform']);
        $hasFavicon = isset($this->request['favicon']);

        if(($hasTransform || $hasFavicon) && substr($type, 0, 6) == 'image/') {
            $descriptor = new neon\raster\Descriptor($absolutePath, $type);

            if($hasTransform) {
                $descriptor->applyTransformation($this->request['transform']);
            }

            if($hasFavicon && preg_match('/MSIE ([0-9]{1,}[\.0-9]{0,})/', $this->http->getUserAgent())) {
                $descriptor->toIcon(16, 32);
            }

            $absolutePath = $descriptor->getLocation();
            $type = $descriptor->getContentType();
            $fileName = $descriptor->getFileName();
        }

        switch($type) {
            case 'text/x-sass':
            case 'text/x-scss':
                if(isset($this->request['compile'])) {
                    $bridge = new aura\css\SassBridge($this->context, $absolutePath);
                    return $bridge->getHttpResponse();
                }
                break;

            case 'application/x-sass-map':
                $bridge = new aura\css\SassBridge($this->context, $absolutePath);
                return $bridge->getMapHttpResponse();
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
