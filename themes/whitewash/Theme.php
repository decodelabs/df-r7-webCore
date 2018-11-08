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

class Theme extends aura\theme\Base
{
    const APPLICATION_IMAGE = 'app.png';

    public function applyDefaultIncludes(aura\view\IView $view)
    {
        $view
            ->linkCss('theme://sass/style.scss')
            ->linkFavicon('theme://favicon.ico')
            ;

        $view->dfKit->load('admin/main');
    }
}
