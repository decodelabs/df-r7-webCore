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
        $key = $this->data->hexHash($this->application->getPassKey());

        if($key != $this->request['key']) {
            throw core\Error::{'EForbidden,EValue'}([
                'message' => 'Pass key is invalid',
                'http' => 403
            ]);
        }
    }
}