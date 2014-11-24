<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\ui\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;

class HttpView extends arch\Action {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml() {
        if($this->application->isProduction()) {
            $this->throwError(401, 'Dev mode only');
        }
        
        $path = new arch\Request($this->request->query['path']);
        $path = '#'.$path->getController().'/'.ucfirst($path->getAction()).'.html';

        $view = $this->aura->getView($path);
        $context = $view->getContext();
        $context->request = $context->location = new arch\Request('~front/');

        $themeConfig = aura\theme\Config::getInstance();
        $view->setTheme($themeConfig->getThemeIdFor('front'));
        
        return $view;
    }
}