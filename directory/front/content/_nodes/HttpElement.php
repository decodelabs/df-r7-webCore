<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\content\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpElement extends arch\node\Base {

    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function executeAsHtml() {
        $view = $this->apex->newWidgetView();
        $view->content->push($view->nightfire->renderElement($this->request['element']));

        return $view->setLayout('Blank')
            ->shouldRenderBase(false);
    }
}