<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\menus\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

use DecodeLabs\Exceptional;

class HttpIndex extends arch\node\Base
{
    public function executeAsHtml()
    {
        $view = $this->apex->view('Index.html');
        $source = arch\navigation\menu\source\Base::factory($this->context, 'directory');

        if (!$source instanceof arch\navigation\menu\source\Directory) {
            throw Exceptional::Logic(
                'Source is not a directory type', null, $source
            );
        }

        $area = trim($this->request->query->get('area', 'admin'), ':~');

        if (empty($area)) {
            $area = null;
        }

        $view['areaFilter'] = $area;
        $view['areaList'] = [$this->_('- All -')] + $source->getAreaOptionList();
        $view['menuList'] = $source->loadNestedMenus($area);

        return $view;
    }
}
