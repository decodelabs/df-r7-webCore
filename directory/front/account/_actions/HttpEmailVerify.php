<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\account\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpEmailVerify extends arch\Action {

    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute() {
        $result = $this->data->user->emailVerify->verify(
            $this->request->query['user'],
            $this->request->query['key']
        );

        if($result) {
            $this->comms->flash(
                'emailVerify.success',
                $this->_('Your email address has been successfully verified'),
                'success'
            );
        } else {
            $this->comms->flash(
                'emailVerify.fail',
                $this->_('The verification link you followed does not appear to be valid any more!'),
                'error'
            );
        }


        return $this->http->defaultRedirect('account/');
    }
}