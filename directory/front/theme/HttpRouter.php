<?php

namespace df\apex\directory\front\theme;

use df\core;
use df\arch;

class HttpRouter implements arch\IRouter {
    
    public function routeIn(arch\IRequest $request) {
        $path = $request->getPath();
        $path->shift();
        
        $query = $request->getQuery();
        $query->theme = $path->shift();
        
        $request->setPath('theme/'.$path->shift());
        $query->file = (string)$path;
        
        return $request;
    }
    
    public function routeOut(arch\IRequest $request) {
        $path = $request->getPath();
        $query = $request->getQuery();
        
        if(!isset($query['theme']) || !isset($query['file'])) {
            return false;
        }
        
        $path->put(1, $query->get('theme'));
        $path->push($query->get('file'));
        $query->remove('theme')->remove('file');
        
        return $request;
    }
}