<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\migrate\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpHello extends arch\node\RestApi {

    public function executeGet() {
        $nodes = [];

        foreach(df\Launchpad::$loader->lookupClassList('apex/directory/devtools/migrate/_nodes') as $name => $class) {
            if(0 !== strpos($name, 'Http')) {
                continue;
            }

            $nodes[] = arch\Request::formatNode(substr($name, 4));
        }

        return [
            'baseUrl' => $this->application->getRouter()->getBaseUrl(),
            'nodes' => $nodes
        ];
    }

    public function authorizeRequest() {
        $key = $this->data->hexHash($this->application->getPassKey());

        if($key != $this->request['key']) {
            throw core\Error::{'EForbidden,EValue'}([
                'message' => 'Pass key is invalid',
                'http' => 403
            ]);
        }
    }
}