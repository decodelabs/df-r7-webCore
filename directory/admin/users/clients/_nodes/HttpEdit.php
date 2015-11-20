<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\clients\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpEdit extends HttpAdd {

    protected function init() {
        $this->_client = $this->scaffold->getRecord();
    }

    protected function getInstanceId() {
        return $this->_client['id'];
    }

    protected function setDefaultValues() {
        $this->values->importFrom($this->_client, [
            'email', 'fullName', 'nickName', 'status',
            'timezone', 'country', 'language'
        ]);

        $this['groups']->setSelected(
            $this->_client->groups->selectFromBridge('group')->toList('group')
        );

        if(!$this->values['nickName']) {
            $parts = explode(' ', $this->values['fullName'], 2);
            $this->values->nickName = array_shift($parts);
        }

        if($this->values['timezone'] == 'UTC') {
            $this->values->timezone = $this->i18n->timezones->suggestForCountry($this->values['country']);
        }
    }
}