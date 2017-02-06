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
use df\flex;
use df\neon;

class HttpUploaded extends arch\node\Base {

    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute() {
        $path = $this->application->getLocalStoragePath().'/upload';
        $path .= core\uri\Path::normalizeLocal(
            '/'.flex\Guid::factory($this->request['id']).
            '/'.$this->format->fileName($this->request['file'])
        );

        if(!file_exists($path)) {
            $this->throwError(404, 'File not found');
        }

        $contentType = null;
        $fileName = basename($path);

        if(isset($this->request['transform'])) {
            $cache = neon\raster\Cache::getInstance();
            $path = $cache->getTransformationFilePath($path, $this->request['transform']);
            $contentType = 'image/png';
        }

        $output = $this->http->fileResponse($path)
            ->setFileName($fileName);

        if($contentType) {
            $output->setContentType($contentType);
        }

        $output->getHeaders()
            ->set('Access-Control-Allow-Origin', '*')
            ->setCacheAccess('public')
            ->canStoreCache(true)
            ->setCacheExpiration('+10 minutes');


        return $output;
    }
}