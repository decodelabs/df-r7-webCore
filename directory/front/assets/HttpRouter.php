<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\assets;

use df\arch;

class HttpRouter implements arch\IRouter
{
    public function routeIn(arch\IRequest $request)
    {
        $path = $request->getPath();
        $path->shift();

        $query = $request->getQuery();

        $request->setPath('assets/download');
        $query->file = (string)$path;

        return $request;
    }

    public function routeOut(arch\IRequest $request)
    {
        $path = $request->getPath();
        $query = $request->getQuery();

        if (!isset($query['file'])) {
            return false;
        }

        $path->set(-1, $query->get('file'));
        $query->remove('file');

        return $request;
    }
}
