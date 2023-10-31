<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\migrate\_nodes;

use DecodeLabs\Exceptional;
use DecodeLabs\R7\Legacy;
use df\arch;

class HttpHello extends arch\node\RestApi
{
    public function executeGet()
    {
        $nodes = [];

        foreach (Legacy::getLoader()->lookupClassList('apex/directory/devtools/migrate/_nodes') as $name => $class) {
            if (0 !== strpos($name, 'Http')) {
                continue;
            }

            $nodes[] = arch\Request::formatNode(substr($name, 4));
        }

        return [
            'baseUrl' => Legacy::$http->getRouter()->getBaseUrl(),
            'nodes' => $nodes
        ];
    }

    public function authorizeRequest()
    {
        $key = Legacy::hexHash(Legacy::getPassKey());

        if ($key != $this->request['key']) {
            throw Exceptional::{'Forbidden,UnexpectedValue'}([
                'message' => 'Pass key is invalid',
                'http' => 403
            ]);
        }
    }
}
