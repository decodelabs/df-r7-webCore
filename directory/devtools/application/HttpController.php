<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\application;

use df;
use df\core;
use df\arch;
use df\halo;

class HttpController extends arch\Controller {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;
    
    public function indexHtmlAction() {
        $view = $this->aura->getWidgetContainer();
        $view->addBlockMenu('directory://~devtools/application/Index');

        return $view;
    }

    public function statsHtmlAction() {
        clearstatcache();
        
        $counter = new core\io\fileStats\Counter();
        $packages = df\Launchpad::$loader->getPackages();
        
        foreach($packages as $name => $package) {
            $location = $package->path;
            $blackList = array();
            
            switch($name) {
                case 'app':
                    $blackList = array(
                        $location.'/data',
                        $location.'/static'
                    );
                    
                    break;
                    
                case 'root':
                    $blackList = array(
                        $location.'/base/libraries/core/i18n/module/cldr',
                    );
                    
                    break;
            }
            
            $counter->addLocation(new core\io\fileStats\Location($name, $location, $blackList));
        }
        
        
        $counter->run();
        
        $view = $this->aura->getView('Stats.html')
            ->setArg('counter', $counter)
            ->setArg('packages', $packages);
            
        return $view;
    }


    public function compileAction() {
        $view = $this->aura->getView('CompileResult.html');
        $view['result'] = halo\process\Base::launchTask('build-app');

        return $view;
    }
}