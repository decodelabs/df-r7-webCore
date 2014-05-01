<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\ui;

use df;
use df\core;
use df\arch;

class HttpRouter implements arch\IRouter {
    
    public function routeIn(arch\IRequest $request) {
        if($request->eq('~ui/index')) {
            return $request;
        }

        $path = $request->getPath();
        $path->shift();

        $query = $request->getQuery();
        $query->path = (string)$path;
        
        $request->setPath('~ui/view');

        return $request;
    }
    
    public function routeOut(arch\IRequest $request) {
        if(!isset($request->query->path)) {
            return $request;
        }

        $path = $request->query['path'];
        return new arch\Request('~ui/'.$path);
    }
}