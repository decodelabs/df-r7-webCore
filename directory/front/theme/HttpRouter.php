<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\theme;

use df\arch;

class HttpRouter implements arch\IRouter
{
    public function routeIn(arch\IRequest $request)
    {
        $path = $request->getPath();
        $path->shift();

        $query = $request->getQuery();
        $query->theme = $path->shift();
        
        $request->setPath('theme/download');
        $query->file = (string)$path;

        return $request;
    }
    
    public function routeOut(arch\IRequest $request)
    {
        $path = $request->getPath();
        $query = $request->getQuery();

        if (!isset($query['theme']) || !isset($query['file'])) {
            return false;
        }
        
        $path->set(-1, $query->get('theme'));
        $path->push($query->get('file'));
        $query->remove('theme')->remove('file');
        
        return $request;
    }
}
