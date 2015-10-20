<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\models\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\axis;
use df\halo;

class HttpRebuildTable extends arch\form\template\Confirm {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    protected $_inspector;

    protected function init() {
        $probe = new axis\introspector\Probe();

        if(!$this->_inspector = $probe->inspectUnit($this->request['unit'])) {
            $this->throwError(404, 'Unit not found');
        }

        if($this->_inspector->getType() != 'table') {
            $this->throwError(403, 'Unit not a table');
        }
    }

    protected function getInstanceId() {
        return $this->_inspector->getId();
    }

    protected function getMainMessage() {
        return $this->_('Are you sure you want to rebuild this table?');
    }

    protected function createItemUi($container) {
        $container->addAttributeList($this->_inspector)
            // Id
            ->addField('id', function($inspector) {
                return $inspector->getId();
            })

            // Canonical id
            ->addField('canonicalId', $this->_('Storage name'), function($inspector) {
                return $inspector->getCanonicalId();
            })

            // Type
            ->addField('type', function($inspector) {
                $output = ucfirst($inspector->getType());

                if($inspector->isVirtual()) {
                    $output = [
                        $output, ' ',
                        $this->html('sup', '(virtual)')
                    ];
                }

                return $output;
            })

            // Adapter
            ->addField('adapter', function($inspector) {
                return $inspector->getAdapterName();
            })

            // Connection
            ->addField('connection', function($inspector) {
                return $inspector->getAdapterConnectionName();
            });
    }

    protected function customizeMainButton($button) {
        $button->setBody($this->_('Rebuild'))
            ->setIcon('refresh');
    }

    protected function apply() {
        $task = 'axis/rebuild-table?unit='.$this->_inspector->getGlobalId();
        return $this->task->initiateStream($task);
    }
}