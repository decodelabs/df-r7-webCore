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
        $path = $this->app->getLocalDataPath().'/upload';
        $path .= core\uri\Path::normalizeLocal(
            '/'.flex\Guid::factory($this->request['id']).
            '/'.str_replace('/', '_', $this->request['file'])
        );

        if(!file_exists($path)) {
            throw core\Error::{'core/fs/ENotFound'}([
                'message' => 'File not found',
                'http' => 404
            ]);
        }

        $contentType = null;
        $fileName = basename($path);

        if(isset($this->request['transform'])) {
            $fileStore = neon\raster\FileStore::getInstance();
            $path = $fileStore->getTransformationFilePath($path, $this->request['transform']);
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
