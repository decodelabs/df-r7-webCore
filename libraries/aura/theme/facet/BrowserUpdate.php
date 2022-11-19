<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\aura\theme\facet;

use df\aura;

class BrowserUpdate extends Base
{
    public function afterHtmlViewRender(aura\view\IHtmlView $view)
    {
        $view
            ->addFootScript(
                'browserUpdate',
                'var $buoop = {};' .
                '$buoop.ol = window.onload;' .
                'window.onload=function(){' .
                    'try {if ($buoop.ol) $buoop.ol();}catch (e) {}' .
                    'var e = document.createElement("script");' .
                    'e.setAttribute("type", "text/javascript");' .
                    'e.setAttribute("src", "//browser-update.org/update.js");' .
                    'document.body.appendChild(e);' .
                '}'
            );
    }
}
