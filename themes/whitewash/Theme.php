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

    protected $_dependencies = [
        'jquery#~2.1'
    ];

    public function applyDefaultIncludes(aura\view\IView $view) {
        $view
            ->linkCss('theme://sass/style.scss')
            ->linkJs('dependency://jquery')
            ->linkJs('theme://js/main.js')
            ->linkFavicon('theme://favicon.ico')
            ;
    }
}