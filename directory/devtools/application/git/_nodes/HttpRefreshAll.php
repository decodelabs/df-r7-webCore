<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\application\git\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpRefreshAll extends arch\node\Base {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml() {
        $model = $this->data->getModel('package');
        $model->updateRemotes();

        $this->comms->flashSuccess(
            'package.update',
            $this->_('All package repositories have been refreshed')
        );

        return $this->http->defaultRedirect();
    }
}