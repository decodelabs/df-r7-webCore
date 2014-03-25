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

class HttpPurgeTableBackups extends arch\form\template\Confirm {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;
    
    protected $_inspector;

    protected function _init() {
        $probe = new axis\introspector\Probe($this->application);

        if(!$this->_inspector = $probe->inspectUnit($this->request->query['unit'])) {
            $this->throwError(404, 'Unit not found');
        }

        if($this->_inspector->getType() != 'table') {
            $this->throwError(401, 'Unit not a table');
        }
    }

    protected function _getDataId() {
        return $this->_inspector->getId();
    }

    protected function _getMainMessage($itemName) {
        return $this->_('Are you sure you want to delete all backups for this table?');
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
                        $this->html->element('sup', '(virtual)')
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
        return $this->_('Delete');
    }

    protected function _getMainButtonIcon() {
        return 'delete';
    }

    protected function _apply() {
        $view = $this->aura->getView('UnitTaskResult.html');
        $view['unit'] = $this->_inspector;
        $view['title'] = $this->_('Backup purge');
        $view['result'] = halo\process\Base::launchTask('axis/purge-table-backups?unit='.$this->_inspector->getId());

        return $view;
    }
}