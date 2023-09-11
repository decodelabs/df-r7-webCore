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
        $theme = $this->view->getTheme();

        if (!$path = $theme->findAsset('zest/manifest.json')) {
            return;
        }

        $manifest = Manifest::load($path);

        foreach ($manifest->getCssData() as $file => $tag) {
            $this->view->linkCss($file, null, $tag);
        }

        foreach ($manifest->getHeadJsData() as $file => $tag) {
            $this->view->linkJs($file, null, $tag);
        }

        foreach ($manifest->getBodyJsData() as $file => $tag) {
            $this->view->linkFootJs($file, null, $tag);
        }

        if ($manifest->isHot()) {
            $this->view->bodyTag->addClass('zest-dev preload');
        }
    }
}
