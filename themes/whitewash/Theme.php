<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\themes\whitewash;

use df;
use df\core;
use df\apex;
use df\aura;

class Theme extends aura\theme\Base {
    
    public function applyDefaultIncludes(aura\view\IView $view) {
        $view
            ->linkCss($view->uri->themeAsset('sass/style.scss'))
            ->linkJs('//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js')
            ->linkJs($view->uri->themeAsset('js/main.js'))
            ->linkFavicon($view->uri->themeAsset('favicon.ico', 'shared'))
            ;
    }
}