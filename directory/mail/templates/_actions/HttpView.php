<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\mail\templates\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;

class HttpView extends arch\Action {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml() {
        $mail = $this->directory->getComponent('~mail/'.$this->request->query['path']);

        if(!$mail instanceof arch\IMailComponent) {
            $this->throwError(403, 'Component is not a Mail object');
        }

        $notification = $mail->renderPreview()->toNotification();
        $html = $notification->getBodyHtml();

        $container = $this->aura->getWidgetContainer();
        $view = $container->getView();
        $view->setTheme(false);
        $container->push($view->html->string($html));
        $view->shouldUseLayout(false);
        $view->setTitle($notification->getSubject());

        return $view;
    }
}