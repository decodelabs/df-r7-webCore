<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\account\_nodes;

use DecodeLabs\R7\Legacy;

use df\arch;

class HttpEmailVerify extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute()
    {
        $result = $this->data->user->emailVerify->verify(
            $this->request['user'],
            $this->request['key']
        );

        if ($result) {
            $this->comms->flashSuccess(
                'emailVerify.success',
                $this->_('Your email address has been successfully verified')
            );
        } else {
            $this->comms->flashError(
                'emailVerify.fail',
                $this->_('The verification link you followed does not appear to be valid any more!')
            );
        }


        return Legacy::$http->defaultRedirect('account/');
    }
}
