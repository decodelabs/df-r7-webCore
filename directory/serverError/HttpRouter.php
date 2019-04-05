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
        extract($request->getComponents());

        $output = $this->newRequest('~serverError/index.'.strtolower($type));
        $output->query->import([
            'error' => $node
        ]);
        $output->importQuery($query);

        return $output;
    }

    public function routeOut(arch\IRequest $request)
    {
        extract($request->getComponents());

        return $this->newRequest('~serverError/'.$query['error'])
            ->setFragment($request->getFragment())
            ->importQuery($query);
    }
}
