<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\mail\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;

class HttpView extends arch\Action {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml() {
        if($this->application->isProduction()) {
            $this->throwError(401, 'Dev mode only');
        }

        $parts = explode('/', $this->request->query['path']);
        $name = array_pop($parts);
        $location = '~mail/'.implode('/', $parts).'/';

        $mail = $this->directory->getComponent($location.$name);

        if(!$mail instanceof arch\IMailComponent) {
            $this->throwError(403, 'Component is not a Mail object');
        }

        $notification = $mail->renderPreview()->toNotification();
        return $this->http->stringResponse($notification->getBodyHtml(), 'text/html');
    }
}