<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\mail\components;

use df;
use df\core;
use df\arch;

class HttpRouter implements arch\IRouter {

    public function routeIn(arch\IRequest $request) {
        if($request->eq('~mail/components/index')
        || $request->matches('~mail/components/preview')) {
            return $request;
        }

        $path = $request->getPath();
        $path->shift();
        $path->shift();

        $query = $request->getQuery();
        $query->path = (string)$path;

        $request->setPath('~mail/components/view');

        return $request;
    }

    public function routeOut(arch\IRequest $request) {
        if(!isset($request->query->path) || $request->isNode('preview')) {
            return $request;
        }

        $path = $request->query['path'];
        return new arch\Request('~mail/components/'.$path);
    }
}