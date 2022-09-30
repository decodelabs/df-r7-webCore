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

use DecodeLabs\Exceptional;
use DecodeLabs\Genesis;
use DecodeLabs\Typify;

class HttpUploaded extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute()
    {
        $path = Genesis::$hub->getLocalDataPath().'/upload';
        $path .= core\uri\Path::normalizeLocal(
            '/'.flex\Guid::factory($this->request['id']).
            '/'.str_replace('/', '_', $this->request['file'])
        );

        if (!file_exists($path)) {
            throw Exceptional::{'df/core/fs/NotFound'}([
                'message' => 'File not found',
                'http' => 404
            ]);
        }

        $fileName = basename($path);
        $type = Typify::detect($path);
        $hasTransform = isset($this->request['transform']);

        if ($hasTransform && substr($type, 0, 6) == 'image/') {
            $descriptor = new neon\raster\Descriptor($path, $type);
            $descriptor->applyTransformation($this->request['transform']);

            $path = $descriptor->getLocation();
            $type = $descriptor->getContentType();
            $fileName = $descriptor->getFileName();
        }


        $output = $this->http->fileResponse($path)
            ->setFileName($fileName)
            ->setContentType($type);

        $output->getHeaders()
            ->set('Access-Control-Allow-Origin', '*')
            ->setCacheAccess('public')
            ->canStoreCache(true)
            ->setCacheExpiration('+10 minutes');

        return $output;
    }
}
