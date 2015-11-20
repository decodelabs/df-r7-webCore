<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\mail\capture\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpMessage extends arch\node\Base {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml() {
        return $this->apex->view('Message.html', [
                'mail' => $mail = $this->scaffold->getRecord(),
                'message' => $mail->toMessage()
            ])
            ->setLayout('Blank');
    }
}