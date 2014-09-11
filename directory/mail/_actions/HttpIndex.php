<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\mail\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpIndex extends arch\Action {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function execute() {
        if($this->application->isProduction()) {
            $this->throwError(401, 'Dev mode only');
        }

        $view = $this->aura->getView('__Index.html');
        $list = df\Launchpad::$loader->lookupFileListRecursive('apex/directory/mail', 'php', function($path) {
            return false !== strpos($path, '_components');
        });

        $mails = [];

        foreach($list as $name => $filePath) {
            $name = str_replace('_components/', '', substr($name, 0, -4));
            $parts = explode('/', $name);
            $componentName = array_pop($parts);
            $location = '~mail/'.implode('/', $parts).'/';

            try {
                $component = $this->directory->getComponent($componentName, $location);
            } catch(\Exception $e) {
                continue;
            }

            if(!$component instanceof arch\IMailComponent) {
                continue;
            }

            $mails[$name] = $component;
        }

        $view['mails'] = $mails;

        return $view;
    }
}