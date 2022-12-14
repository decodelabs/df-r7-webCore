<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\plug;

use DecodeLabs\Exceptional;
use DecodeLabs\Genesis;
use DecodeLabs\Zest\Manifest;

use df\arch;
use df\aura;

class Zest implements arch\IDirectoryHelper
{
    use arch\TDirectoryHelper;
    use aura\view\TView_DirectoryHelper;

    protected function _init()
    {
        if (!$this->view) {
            throw Exceptional::{'df/aura/view/NoView,NoContext'}(
                'View is not available in plugin context'
            );
        }
    }

    public function __invoke()
    {
        $theme = $this->view->getTheme()->getId();

        $manifest = Manifest::load(
            Genesis::$hub->getApplicationPath() . '/themes/' . $theme . '/assets/manifest.json'
        );

        $cts = Genesis::$build->getCacheBuster();

        foreach ($manifest->getCssData() as $file => $tag) {
            $url = $this->view->uri($file);
            $url->query->cts = $cts;
            $this->view->linkCss($url, null, $tag);
        }

        foreach ($manifest->getHeadJsData() as $file => $tag) {
            $url = $this->view->uri($file);
            $url->query->cts = $cts;
            $this->view->linkJs($url, null, $tag);
        }

        foreach ($manifest->getBodyJsData() as $file => $tag) {
            $url = $this->view->uri($file);
            $url->query->cts = $cts;
            $this->view->linkFootJs($url, null, $tag);
        }

        if ($manifest->isHot()) {
            $this->view->bodyTag->addClass('zest-dev preload');
        }
    }
}
