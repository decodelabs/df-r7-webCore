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

class HttpRefreshAll extends arch\Action {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;
    
    public function executeAsHtml() {
        $model = $this->data->getModel('package');
        $model->updateRemotes();

        $this->comms->flash(
            'package.update',
            $this->_('All package repositories have been refreshed'),
            'success'
        );

        return $this->http->defaultRedirect();
    }
}