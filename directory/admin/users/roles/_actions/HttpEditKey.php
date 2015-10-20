<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\roles\_actions;

use df;
use df\core;
use df\arch;

class HttpEditKey extends HttpAddKey {

    protected function init() {
        $this->_key = $this->data->fetchForAction(
            'axis://user/Key',
            $this->request['key'],
            'edit'
        );

        $this->_role = $this->_key['role'];
    }

    protected function getInstanceId() {
        return $this->_key['id'];
    }

    protected function setDefaultValues() {
        $this->values->importFrom($this->_key, [
            'domain', 'pattern', 'allow'
        ]);
    }
}
