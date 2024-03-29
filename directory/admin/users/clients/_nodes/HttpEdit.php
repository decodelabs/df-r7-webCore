<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\users\clients\_nodes;

use df\arch\node\form\SelectorDelegate;

class HttpEdit extends HttpAdd
{
    protected function init(): void
    {
        $this->_client = $this->scaffold->getRecord();
    }

    protected function getInstanceId(): ?string
    {
        return $this->_client['id'];
    }

    protected function setDefaultValues(): void
    {
        $this->values->importFrom($this->_client, [
            'email', 'fullName', 'nickName', 'status',
            'timezone', 'country', 'language'
        ]);

        /** @var SelectorDelegate $groups */
        $groups = $this['groups'];
        $groups->setSelected(
            $this->_client->groups->selectFromBridge('group')->toList('group')
        );

        if (!$this->values['nickName']) {
            $parts = explode(' ', $this->values['fullName'], 2);
            $this->values->nickName = array_shift($parts);
        }

        if ($this->values['timezone'] == 'UTC') {
            $this->values->timezone = $this->i18n->timezones->suggestForCountry($this->values['country']);
        }
    }
}
