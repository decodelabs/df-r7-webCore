<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\aura\theme\facet;

use df;
use df\core;
use df\aura;
use df\arch;
use df\spur;

class CookieNotice extends Base {
    
    public function onHtmlViewLayoutRender(aura\view\IHtmlView $view, $content) {
        if($view->context->getRunMode() != 'Http' 
        || $view->http->getCookie('cnx')
        || !$view->shouldRenderBase()) {
            return;
        }

        return $view->context->apex->template('~front/#/elements/CookieNotice.html')
            ->renderTo($view)."\n".$content;
    }
}