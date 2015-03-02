<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\migrate;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpController extends arch\Controller {
    
    public function authorizeRequest() {
        $key = bin2hex($this->data->hash($this->application->getPassKey()));

        if($key != $this->request->query['key']) {
            $this->throwError(403, 'Pass key is invalid');
        }
    }
}