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

class DfKit implements arch\IDirectoryHelper {

    use arch\TDirectoryHelper;
    use aura\view\TView_DirectoryHelper;

    protected static $_isInit = false;

    protected function _init() {
        if(!$this->view) {
            throw core\Error::{'aura/view/ENoView,ENoContext'}(
                'View is not available in plugin context'
            );
        }
    }

    public function init() {
        if(self::$_isInit) {
            return $this;
        }

        $url = '/df-kit/bootstrap.js?theme='.$this->view->getTheme()->getId();

        if(df\Launchpad::$compileTimestamp) {
            $url .= '&cts='.df\Launchpad::$compileTimestamp;
        } else {
            $url .= '&cts='.time();
        }

        $this->view->linkJs('dependency://requirejs', 1, [
            'data-main' => $this->view->uri($url)
        ]);

        return $this;
    }

    public function load(...$modules) {
        $this->init();

        $current = $this->view->bodyTag->getDataAttribute('require');
        $modules = core\collection\Util::flatten($modules);

        if(!empty($current)) {
            $modules = array_unique(array_merge(explode(' ', $current), $modules));
        }

        $this->view->bodyTag->setDataAttribute('require', implode(' ', $modules));
        return $this;
    }
}