<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\shared\tasks\_components;

use df;
use df\core;
use df\apex;
use df\arch;

class Invoke extends arch\component\Base {
    
    protected function _execute($request) {
        $token = $this->data->task->invoke->prepareTask($request);
        
        return $this->http->redirect(
            $this->directory->normalizeRequest(
                '~/tasks/invoke?token='.$token, 
                $this->directory->backRequest(null, true)
            )
        );
    }
}