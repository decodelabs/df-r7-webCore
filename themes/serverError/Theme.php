<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\themes\serverError;

use df;
use df\core;
use df\apex;
use df\aura;

class Theme extends aura\theme\Base
{
    const APPLICATION_IMAGE = null;
    const FACETS = [];
    const DEFAULT_FACETS = [];

    public function applyDefaultIncludes(aura\view\IView $view)
    {
        $view
            ->addStyles($view->uri->fetch('theme://serverError#sass/style.scss')->getContents())
            ;
    }
}
