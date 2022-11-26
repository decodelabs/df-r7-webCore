<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\avatar;

use df\arch;

class HttpRouter implements arch\IRouter
{
    public function routeIn(arch\IRequest $request)
    {
        $path = $request->getPath();
        $query = $request->getQuery();

        $parts = explode('-', $path->getFilename());

        if ($path->hasExtension()) {
            $query->type = $path->getExtension();
        }

        $path->setBasename('download');
        $query->user = array_shift($parts);

        if (!empty($parts)) {
            $query->size = array_shift($parts);
        }

        return $request;
    }

    public function routeOut(arch\IRequest $request)
    {
        $path = $request->getPath();
        $query = $request->getQuery();

        $fileName = $query['user'];

        if (isset($query['size'])) {
            $fileName .= '-' . $query['size'];
        }

        if (isset($query['type'])) {
            $path->setExtension($query['type']);
        }

        $path->setFilename($fileName);
        unset($query['user'], $query['size'], $query['type']);

        return $request;
    }
}
