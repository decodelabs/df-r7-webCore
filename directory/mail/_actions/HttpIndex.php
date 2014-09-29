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
        $view = $this->aura->getView('__Index.html');
        $list = df\Launchpad::$loader->lookupFileListRecursive('apex/directory/mail', 'php', function($path) {
            return false !== strpos($path, '_components');
        });

        $mails = [];

        foreach($list as $name => $filePath) {
            $parts = explode('_components/', substr($name, 0, -4), 2);
            $path = array_shift($parts);
            $name = array_shift($parts);

            if(false !== strpos($name, '/')) {
                $path .= '#/';
            }

            $name = $path.$name;
            $path = '~mail/'.$name;
                $component = $this->directory->getComponent($path);

            try {
            } catch(\Exception $e) {
                $mails[$name] = null;
                continue;
            }

            if(!$component instanceof arch\IMailComponent) {
                continue;
            }

            $mails[$name] = $component;
        }

        ksort($mails);
        $view['mails'] = $mails;

        return $view;
    }
}