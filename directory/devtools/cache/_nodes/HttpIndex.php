<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\cache\_nodes;

use DecodeLabs\Stash;
use df\arch;

class HttpIndex extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml()
    {
        $view = $this->apex->newWidgetView();
        $view->content->push($this->apex->component('~devtools/cache/IndexHeaderBar'));

        $view->content->push(
            $view->html->attributeList(Stash::load('test'))
                ->addField('Total cache items', function ($cache) {
                    return count($cache);
                })
        );

        $view->content->addBlockMenu('directory://~devtools/cache/Index')
            ->shouldShowDescriptions(false);

        return $view;
    }
}
