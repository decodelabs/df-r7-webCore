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

use DecodeLabs\Exceptional;

class HttpView extends arch\node\Base
{
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml()
    {
        if ($this->app->isProduction()) {
            throw Exceptional::Forbidden([
                'message' => 'Dev mode only',
                'http' => 403
            ]);
        }

        $path = new arch\Request($this->request['path']);
        $path = '#'.$path->getController().'/'.ucfirst($path->path->getFileName()).'.html';

        $view = $this->apex->view($path);
        $context = $view->getContext();
        $context->request = $context->location = new arch\Request('~front/');

        $themeConfig = aura\theme\Config::getInstance();
        $view->setTheme($themeConfig->getThemeIdFor('front'));

        return $view;
    }
}
