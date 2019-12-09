<?php

namespace df\apex\directory\front\media;

use df\core;
use df\arch;

class HttpRouter implements arch\IRouter
{
    public function routeIn(arch\IRequest $request)
    {
        if ($request->isNode('uploaded')) {
            return $request;
        }

        $parts = explode('[', $request->path->pop(), 2);
        $id = rtrim((string)array_shift($parts), '-|');

        if ($id[0] == 'f') {
            $request->query->file = substr($id, 1);
        } elseif ($id[0] == 'v') {
            $request->query->version = substr($id, 1);
        } else {
            $request->query->version = $id;
        }

        if (!empty($parts)) {
            $request->query->transform = '['.array_shift($parts);
        }

        return $request;
    }

    public function routeOut(arch\IRequest $request)
    {
        if ($request->isNode('uploaded')) {
            return $request;
        }

        $query = $request->getQuery();

        if (isset($query->version)) {
            $request->path->push('v'.$query['version']);
        } elseif (isset($query->file)) {
            $request->path->push('f'.$query['file']);
        } else {
            return $request;
        }

        if (isset($query->transform)) {
            $last = $request->path->getBasename();
            $transform = $query['transform'];

            if (substr($transform, 0, 1) != '[') {
                $last = rtrim($last, '|');
                $transform = '|'.$transform;
            }

            $last .= $transform;
            $request->path->setBasename($last);
        }

        unset($query->file, $query->version, $query->transform);
        //$request->setQuery(null);
        return $request;
    }
}
