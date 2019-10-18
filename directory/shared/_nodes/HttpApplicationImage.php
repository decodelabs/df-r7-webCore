<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\shared\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\neon;

use DecodeLabs\Atlas;

class HttpApplicationImage extends arch\node\Base
{
    const DEFAULT_ACCESS = arch\IAccess::ALL;
    const CHECK_ACCESS = false;

    public function executeAsPng()
    {
        $theme = $this->apex->getTheme();

        if (!$path = $theme->getApplicationImagePath()) {
            throw core\Error::{'core/fs/ENotFound'}([
                'message' => 'No application image path set',
                'http' => 404
            ]);
        }

        $absPath = $theme->findAsset($path);
        $type = Atlas::$mime->detect($absPath);

        if (!$absPath) {
            throw core\Error::{'core/fs/ENotFound'}([
                'message' => 'Application image '.$path.' not found',
                'http' => 404
            ]);
        }

        $descriptor = (new neon\raster\Descriptor($absPath, $type))
            ->setFileName($this->http->request->url->path->getFileName())
            ->shouldIncludeTransformationInFileName(false);

        if (isset($this->request['width'])) {
            $width = $this->request['width'];
            $height = $this->request->query->get('height', $width);
            $descriptor->applyTransformation('[rs:'.$width.'|'.$height.']');
        }

        return $this->http->fileResponse($descriptor->getLocation())
            ->setFileName($descriptor->getFileName())
            ->setContentType($descriptor->getContentType());
    }
}
