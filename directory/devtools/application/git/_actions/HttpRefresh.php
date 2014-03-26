<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\application\git\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\spur;

class HttpRefresh extends arch\Action {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml() {
        $model = $this->data->getModel('package');
        $name = $this->request->query['package'];
        
        if(!$model->updateRemote($name)) {
            $this->comms->flash(
                'git.update',
                $this->_('Package "%n%" could not be updated', ['%n%' => $name]),
                'error'
            );
        } else {
            $this->comms->flash(
                'git.update',
                $this->_('Package "%n%" has been successfully refreshed', ['%n%' => $name]),
                'success'
            );
        }

        return $this->http->defaultRedirect();
    }
}