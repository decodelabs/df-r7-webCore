<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\models\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\axis;
use df\halo;

use DecodeLabs\Tagged\Html;
use DecodeLabs\Exceptional;

class HttpPurgeTableBackups extends arch\node\ConfirmForm
{
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    protected $_inspector;

    protected function init()
    {
        $probe = new axis\introspector\Probe();

        if (!$this->_inspector = $probe->inspectUnit($this->request['unit'])) {
            throw Exceptional::{'df/axis/unit/NotFound'}([
                'message' => 'Unit not found',
                'http' => 404
            ]);
        }

        if ($this->_inspector->getType() != 'table') {
            throw Exceptional::{'df/axis/unit/Domain,Forbidden'}([
                'message' => 'Unit not a table',
                'http' => 403
            ]);
        }
    }

    protected function getInstanceId()
    {
        return $this->_inspector->getId();
    }

    protected function getMainMessage()
    {
        return $this->_('Are you sure you want to delete all backups for this table?');
    }

    protected function createItemUi($container)
    {
        $container->addAttributeList($this->_inspector)
            // Id
            ->addField('id', function ($inspector) {
                return $inspector->getId();
            })

            // Canonical id
            ->addField('canonicalId', $this->_('Storage name'), function ($inspector) {
                return $inspector->getCanonicalId();
            })

            // Type
            ->addField('type', function ($inspector) {
                $output = ucfirst($inspector->getType());

                if ($inspector->isVirtual()) {
                    $output = [
                        $output, ' ',
                        Html::{'sup'}('(virtual)')
                    ];
                }

                return $output;
            })

            // Adapter
            ->addField('adapter', function ($inspector) {
                return $inspector->getAdapterName();
            })

            // Connection
            ->addField('connection', function ($inspector) {
                return $inspector->getAdapterConnectionName();
            });
    }

    protected function customizeMainButton($button)
    {
        $button->setBody($this->_('Delete'))
            ->setIcon('delete');
    }

    protected function apply()
    {
        return $this->task->initiateStream('axis/purge-table-backups?unit='.$this->_inspector->getId());
    }
}
