<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\ui\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;

class HttpView extends arch\node\Base {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml() {
        if($this->application->isProduction()) {
            $this->throwError(401, 'Dev mode only');
        }

        $path = new arch\Request($this->request['path']);
        $path = '#'.$path->getController().'/'.ucfirst($path->getNode()).'.html';

        $view = $this->apex->view($path);
        $context = $view->getContext();
        $context->request = $context->location = new arch\Request('~front/');

        $themeConfig = aura\theme\Config::getInstance();
        $view->setTheme($themeConfig->getThemeIdFor('front'));

        return $view;
    }
}