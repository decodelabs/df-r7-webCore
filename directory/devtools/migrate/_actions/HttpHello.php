<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\migrate\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpHello extends arch\restApi\Action {

    public function executeGet() {
        $actions = [];

        foreach(df\Launchpad::$loader->lookupClassList('apex/directory/devtools/migrate/_actions') as $name => $class) {
            if(0 !== strpos($name, 'Http')) {
                continue;
            }

            $actions[] = arch\Request::formatAction(substr($name, 4));
        }

        return [
            'baseUrl' => $this->application->getRouter()->getBaseUrl(),
            'actions' => $actions
        ];
    }
}