<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\models\_nodes;

use DecodeLabs\Exceptional;
use DecodeLabs\Tagged as Html;

use df\arch;
use df\axis;

class HttpRebuildTable extends arch\node\ConfirmForm
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;

    protected $_inspector;

    protected function init(): void
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

    protected function getInstanceId(): ?string
    {
        return $this->_inspector->getId();
    }

    protected function getMainMessage()
    {
        return $this->_('Are you sure you want to rebuild this table?');
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
        $button->setBody($this->_('Rebuild'))
            ->setIcon('refresh');
    }

    protected function apply()
    {
        return $this->task->initiateStream('axis/rebuild-table?unit=' . $this->_inspector->getId());
    }
}
