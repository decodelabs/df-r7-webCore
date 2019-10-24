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

use DecodeLabs\Glitch;
use DecodeLabs\Atlas;

class HttpDownload extends arch\node\Base
{
    const OPTIMIZE = true;
    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute()
    {
        $theme = aura\theme\Base::factory($this->request['theme']);
        $assetPath = core\uri\Path::normalizeLocal($this->request['file']);
        $type = null;
        $cacheAge = $this->app->isDevelopment() ? null : '2 days';

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
            throw Glitch::{'df/core/fs/ENotFound'}([
                'message' => 'File not found',
                'http' => 404
            ]);
        }

        if (!$type) {
            $type = Atlas::$mime->detect($absolutePath);
        }

        $hasTransform = isset($this->request['transform']);
        $hasFavicon = isset($this->request['favicon']);

        if (($hasTransform || $hasFavicon) && substr($type, 0, 6) == 'image/') {
            $descriptor = new neon\raster\Descriptor($absolutePath, $type);

            if ($hasTransform) {
                $descriptor->applyTransformation($this->request['transform']);
            }

            if ($hasFavicon && preg_match('/MSIE ([0-9]{1,}[\.0-9]{0,})/', $this->http->getUserAgent())) {
                $descriptor->toIcon(16, 32);
            }

            $absolutePath = $descriptor->getLocation();
            $type = $descriptor->getContentType();
            $fileName = $descriptor->getFileName();
        }

        switch ($type) {
            case 'text/x-sass':
            case 'text/x-scss':
                $bridge = new aura\css\SassBridge($this->context, $absolutePath);
                return $bridge->getHttpResponse();

            case 'application/x-sass-map':
                $bridge = new aura\css\SassBridge($this->context, $absolutePath);
                return $bridge->getMapHttpResponse();

            default:
                $output = $this->http->fileResponse($absolutePath);
                $output->setContentType($type);
        }

        $output->setFileName($fileName, isset($this->request['attachment']))
            ->getHeaders()
                ->set('Access-Control-Allow-Origin', '*');

        if ($cacheAge) {
            $output->getHeaders()->setCacheExpiration($cacheAge);
        }

        return $output;
    }
}
