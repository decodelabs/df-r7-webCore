<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front;

use df\arch;
use df\core;

class HttpRootNodeRouter extends core\app\http\RootNodeRouter
{
    protected $_matches = [
        '/^(apple-)?touch-icon(.+)?\.png/' => 'appleTouchIcon'
    ];

    public function appleTouchIcon(arch\IRequest $request, $matches)
    {
        $request->path->setFilename('application-image');

        if (isset($matches[2])) {
            $parts = explode('-', trim($matches[2], '-'));

            foreach ($parts as $part) {
                if ($part == 'precomposed') {
                    $request->query->precomposed = true;
                } elseif (preg_match('/^([0-9]+)x([0-9]+)$/', $part, $sizes)) {
                    $request->query->width = $sizes[1];
                    $request->query->height = $sizes[2];
                }
            }
        } else {
            $request->query->width = 180;
            $request->query->height = 180;
        }

        return $request;
    }
}
