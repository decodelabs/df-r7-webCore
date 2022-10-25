<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\users\roles\_nodes;

use df\arch;

class HttpDeleteKey extends arch\node\DeleteForm
{
    public const ITEM_NAME = 'key';

    protected $_key;

    protected function init(): void
    {
        $this->_key = $this->data->fetchForAction(
            'axis://user/Key',
            $this->request['key']
        );
    }

    protected function getInstanceId(): ?string
    {
        return $this->_key['id'];
    }

    protected function createItemUi($container)
    {
        $container->addAttributeList($this->_key)

            // Role
            ->addField('role', function ($row) {
                return $row['role']['name'];
            })

            // Domain
            ->addField('domain')

            // Pattern
            ->addField('pattern')

            // Allow
            ->addField('allow', $this->_('Policy'), function ($row) {
                return $row['allow'] ? $this->_('Allow') : $this->_('Deny');
            });
    }

    protected function apply()
    {
        $this->_key->delete();
        $this->user->instigateGlobalKeyringRegeneration();
    }
}
