<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\error;

use df;
use df\core;
use df\apex;
use df\arch;
use df\halo;

class TaskController extends arch\Controller {
    
    const CHECK_ACCESS = false;
    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function defaultAction() {
        $request = $this->_context->request;
        
        if(!$request instanceof arch\IErrorRequest) {
            $this->throwError(404, 'You shouldn\'t be here');
        }

        $exception = $request->getException();
        $code = $exception->getCode();
        $lastRequest = $request->getLastRequest();

        if(!halo\protocol\http\response\HeaderCollection::isValidStatusCode($code)
        || !halo\protocol\http\response\HeaderCollection::isErrorStatusCode($code)) {
            $code = 500;
        }

        try {
            switch($code) {
                case 401:
                case 403:
                    $this->data->log->accessError($code, $lastRequest, $exception->getMessage());
                    break;
                        
                case 404:
                    $this->data->log->notFound($lastRequest, $exception->getMessage());
                    break;

                case 500:
                    $this->data->log->exception($exception, $lastRequest->toString());
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