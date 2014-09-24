<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\mail;

use df;
use df\core;
use df\arch;

class HttpRouter implements arch\IRouter {
    
    public function routeIn(arch\IRequest $request) {
        if($request->eq('~mail/index')) {
            return $request;
        }

        $path = $request->getPath();
        $path->shift();

        $query = $request->getQuery();
        $query->path = (string)$path;
        
        $request->setPath('~mail/view');

        return $request;
    }
    
    public function routeOut(arch\IRequest $request) {
        if(!isset($request->query->path)) {
            return $request;
        }

        $path = $request->query['path'];
        return new arch\Request('~mail/'.$path);
    }
}