<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\serverError;

use df;
use df\core;
use df\apex;
use df\arch;
use df\flex;

class HttpRouter extends arch\router\Base
{
    public function routeIn(arch\IRequest $request)
    {
        $type = $request->getType();
        $node = $request->getRawNode();
        $query = $request->getQuery();

        $output = $this->newRequest('~serverError/index.'.strtolower((string)$type));
        
        $output->query->import([
            'error' => $node
        ]);

        $output->importQuery($query);

        return $output;
    }

    public function routeOut(arch\IRequest $request)
    {
        $query = $request->getQuery();

        return $this->newRequest('~serverError/'.$query['error'])
            ->setFragment($request->getFragment())
            ->importQuery($query);
    }
}
