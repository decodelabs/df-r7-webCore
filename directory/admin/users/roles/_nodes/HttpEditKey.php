<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\users\roles\_nodes;

class HttpEditKey extends HttpAddKey
{
    protected function init()
    {
        $this->_key = $this->data->fetchForAction(
            'axis://user/Key',
            $this->request['key']
        );

        $this->_role = $this->_key['role'];
    }

    protected function getInstanceId()
    {
        return $this->_key['id'];
    }

    protected function setDefaultValues(): void
    {
        $this->values->importFrom($this->_key, [
            'domain', 'pattern', 'allow'
        ]);
    }
}
