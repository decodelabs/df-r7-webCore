<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\shared\_nodes;

use df\arch;
use df\neon;

use DecodeLabs\Exceptional;
use DecodeLabs\R7\Legacy;
use DecodeLabs\Typify;

class HttpApplicationImage extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::ALL;
    public const CHECK_ACCESS = false;

    public function executeAsPng()
    {
        $theme = $this->apex->getTheme($this->request['theme']);

        if (!$path = $theme->getApplicationImagePath()) {
            throw Exceptional::{'df/core/fs/NotFound'}([
                'message' => 'No application image path set',
                'http' => 404
            ]);
        }

        $absPath = $theme->findAsset($path);
        $type = Typify::detect($absPath);

        if (!$absPath) {
            throw Exceptional::{'df/core/fs/NotFound'}([
                'message' => 'Application image '.$path.' not found',
                'http' => 404
            ]);
        }

        $descriptor = (new neon\raster\Descriptor($absPath, $type))
            ->setFileName(Legacy::$http->getUrl()->path->getFileName())
            ->shouldIncludeTransformationInFileName(false);

        if (isset($this->request['width'])) {
            $width = $this->request['width'];
            $height = $this->request->query->get('height', $width);
            $descriptor->applyTransformation('[rs:'.$width.'|'.$height.']');
        }

        return Legacy::$http->fileResponse($descriptor->getLocation())
            ->setFileName($descriptor->getFileName())
            ->setContentType($descriptor->getContentType());
    }
}
