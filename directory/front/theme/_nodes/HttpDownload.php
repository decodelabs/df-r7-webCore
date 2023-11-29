<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\theme\_nodes;

use DecodeLabs\Exceptional;
use DecodeLabs\Genesis;
use DecodeLabs\R7\Legacy;
use DecodeLabs\Typify;

use df\arch;
use df\aura;
use df\core;
use df\neon;

class HttpDownload extends arch\node\Base
{
    public const OPTIMIZE = true;
    public const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute()
    {
        $theme = aura\theme\Base::factory($this->request['theme']);
        $assetPath = core\uri\Path::normalizeLocal($this->request['file']);
        $type = null;

        if(
            isset($this->request['v']) ||
            isset($this->request['cts'])
        ) {
            $cacheAge = '1 year';
        } else {
            $cacheAge = Genesis::$environment->isDevelopment() ? null : '1 day';
        }

        $fileName = basename($assetPath);
        $parts = explode('.', $fileName);

        if (array_pop($parts) == 'map') {
            switch (array_pop($parts)) {
                case 'scss':
                case 'sass':
                    $type = 'application/x-sass-map';
                    $assetPath = substr($assetPath, 0, -4);
                    break;
            }
        }

        if (!$absolutePath = $theme->findAsset($assetPath)) {
            throw Exceptional::{'df/core/fs/NotFound'}([
                'message' => 'File not found',
                'http' => 404
            ]);
        }

        if (!$type) {
            $type = Typify::detect($absolutePath);
        }

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
        $output->setContentType($type);

        $output->setFileName($fileName, isset($this->request['attachment']))
            ->getHeaders()
                ->set('Access-Control-Allow-Origin', '*');

        if ($cacheAge) {
            $output->getHeaders()->setCacheExpiration($cacheAge);
        }

        return $output;
    }
}
