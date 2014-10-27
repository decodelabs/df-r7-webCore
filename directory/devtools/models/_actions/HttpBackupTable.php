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

class HttpBackupTable extends arch\form\template\Confirm {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    protected $_inspector;

    protected function _init() {
        $probe = new axis\introspector\Probe();

        if(!$this->_inspector = $probe->inspectUnit($this->request->query['unit'])) {
            $this->throwError(404, 'Unit not found');
        }
    }

    protected function _getDataId() {
        return $this->_inspector->getId();
    }

    protected function _getMainMessage($itemName) {
        return $this->_('Are you sure you want to back up this table?');
    }

    protected function _renderItemDetails($container) {
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

    protected function _getMainButtonText() {
        return $this->_('Back up');
    }

    protected function _getMainButtonIcon() {
        return 'backup';
    }

    protected function _apply() {
        $task = 'axis/backup-table?unit='.$this->_inspector->getId();
        return $this->task->initiateStream($task);
    }
}