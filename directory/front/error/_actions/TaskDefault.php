<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\error\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\link;

class TaskDefault extends arch\Action {
    
    const CHECK_ACCESS = false;
    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute() {
        $request = $this->context->request;
        
        if(!$request instanceof arch\IErrorRequest) {
            $this->throwError(404, 'You shouldn\'t be here');
        }

        $exception = $request->getException();
        $code = $exception->getCode();
        $lastRequest = $request->getLastRequest();

        if(!link\http\response\HeaderCollection::isValidStatusCode($code)
        || !link\http\response\HeaderCollection::isErrorStatusCode($code)) {
            $code = 500;
        }

        try {
            switch($code) {
                case 401:
                case 403:
                    $this->logs->logAccessError($code, $lastRequest, $exception->getMessage());
                    break;
                        
                case 404:
                    $this->logs->logNotFound($lastRequest, $exception->getMessage());
                    break;

                case 500:
                    $this->logs->logException($exception, $lastRequest->toString());
                    break;
            }
        } catch(\Exception $e) {
            core\debug()->exception($e);
        }

        core\debug()
            ->exception($exception)
            ->render();
    }
}