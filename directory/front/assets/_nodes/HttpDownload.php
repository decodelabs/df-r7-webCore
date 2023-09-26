<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\assets\_nodes;

use DecodeLabs\Exceptional;
use DecodeLabs\R7\Legacy;
use DecodeLabs\Typify;
use df\arch;

use df\core;
use df\neon;

class HttpDownload extends arch\node\Base
{
    public const OPTIMIZE = true;
    public const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute()
    {
        $path = core\uri\Path::normalizeLocal($this->request['file']);

        if (!$absolutePath = Legacy::getLoader()->findFile('apex/assets/' . $path)) {
            throw Exceptional::{'df/core/fs/NotFound'}([
                'message' => 'File not found',
                'http' => 404
            ]);
        }

        $fileName = basename($absolutePath);
        $type = Typify::detect($absolutePath);

        $hasTransform = isset($this->request['transform']);
        $hasFavicon = isset($this->request['favicon']);

        if (($hasTransform || $hasFavicon) && substr($type, 0, 6) == 'image/') {
            $descriptor = new neon\raster\Descriptor($absolutePath, $type);

            if ($hasTransform) {
                $descriptor->applyTransformation($this->request['transform']);
            }

            if (
                $hasFavicon &&
                preg_match('/MSIE ([0-9]{1,}[\.0-9]{0,})/', Legacy::$http->getUserAgent() ?? '')
            ) {
                $descriptor->toIcon(16, 32);
            }

            $absolutePath = $descriptor->getLocation();
            $type = $descriptor->getContentType();
            $fileName = $descriptor->getFileName();
        }


        $output = Legacy::$http->fileResponse($absolutePath);

        if ($type) {
            $output->setContentType($type);
        }

        $output->setFileName($fileName, isset($this->request['attachment']))
            ->getHeaders()
                ->set('Access-Control-Allow-Origin', '*');

        return $output;
    }
}
