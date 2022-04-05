<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\plug;

use df;
use df\core;
use df\plug;
use df\arch;
use df\aura;

use DecodeLabs\Exceptional;
use DecodeLabs\R7\Legacy;

class DfKit implements arch\IDirectoryHelper
{
    use arch\TDirectoryHelper;
    use aura\view\TView_DirectoryHelper;

    protected static $_isInit = false;

    protected function _init()
    {
        if (!$this->view) {
            throw Exceptional::{'df/aura/view/NoView,NoContext'}(
                'View is not available in plugin context'
            );
        }
    }

    public function init()
    {
        if (self::$_isInit) {
            return $this;
        }

        //$this->initRequire();
        $this->initSystem();

        return $this;
    }


    protected function initRequire()
    {
        $url = '/df-kit/bootstrap-require.js?theme='.$this->view->getTheme()->getId();
        $url .= '&cts='.(Legacy::getCompileTimestamp() ?? time());

        // Avoid using data-main for extensions also using RequireJS
        $this->view->linkJs('dependency://requirejs', 1, [
            '__invoke' => 'require([\''.Legacy::uri($url).'\']);'
        ]);
    }

    protected function initSystem()
    {
        $url = '/df-kit/bootstrap-system.js?theme='.$this->view->getTheme()->getId();
        $url .= '&cts='.(Legacy::getCompileTimestamp() ?? time());

        $url = Legacy::uri($url);
        $jsUrl = (string)$url;
        $url->path->setExtension('json');
        $mapUrl = (string)$url;

        $this->view->linkJs($mapUrl, 2, [
            'type' => 'systemjs-importmap',
            'crossorigin' =>'anonymous',
            '__invoke' =>
                'if (typeof Promise === \'undefined\')'."\n".
                'document.write(\'<script src="https://unpkg.com/bluebird@3.7.2/js/browser/bluebird.core.min.js"><\/script>\');'."\n".
                'if (typeof fetch === \'undefined\')'."\n".
                'document.write(\'<script src="https://unpkg.com/whatwg-fetch@3.4.1/dist/fetch.umd.js"><\/script>\');'
        ]);
        $this->view->linkJs('https://cdn.jsdelivr.net/npm/systemjs/dist/system.js', 3, [
            '__invoke' => 'System.import(\''.$jsUrl.'\');'
        ]);
        $this->view->linkJs('https://cdn.jsdelivr.net/npm/systemjs/dist/extras/named-register.js', 4);
        $this->view->linkJs('https://cdn.jsdelivr.net/npm/systemjs/dist/extras/amd.js', 4);
    }




    public function load(...$modules)
    {
        $this->init();

        $current = $this->view->bodyTag->getDataAttribute('import');
        $modules = core\collection\Util::flatten($modules);

        if (!empty($current)) {
            $modules = array_unique(array_merge(explode(' ', $current), $modules));
        }

        $this->view->bodyTag->setDataAttribute('import', implode(' ', $modules));
        return $this;
    }
}
