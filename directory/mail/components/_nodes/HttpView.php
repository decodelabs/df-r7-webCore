<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\mail\components\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;

class HttpView extends arch\node\Base {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml() {
        $mail = $this->apex->component('~mail/'.$this->request['path']);

        if(!$mail instanceof arch\IMailComponent) {
            $this->throwError(403, 'Component is not a Mail object');
        }

        $notification = $mail->renderPreview()->toNotification();
        $html = $notification->getBodyHtml();

        $view = $this->apex->newWidgetView();
        $view->setTheme(false);
        $view->content->push($view->html->string($html));
        $view->shouldUseLayout(false);
        $view->setTitle($notification->getSubject());

        return $view;
    }
}