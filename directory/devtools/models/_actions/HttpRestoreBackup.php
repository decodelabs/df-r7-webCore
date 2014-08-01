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

class HttpRestoreBackup extends arch\form\template\Confirm {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    protected $_file;

    protected function _init() {
        $fileName = basename($this->request->query['backup']);

        if(!preg_match('/^axis\-[0-9]+\.tar$/i', $fileName)) {
            $this->throwError(403, 'Not an axis backup file');
        }

        $this->_file = $this->application->getSharedStoragePath().'/backup/'.$fileName;

        if(!is_file($this->_file)) {
            $this->throwError(404, 'Backup not found');
        }
    }

    protected function _getDataId() {
        return basename($this->_file);
    }

    protected function _getMainMessage($itemName) {
        return $this->_('Are you sure you want to restore this backup?');
    }

    protected function _renderItemDetails($container) {
        $container->addAttributeList(basename($this->_file))
            ->addField('name', function($backup) {
                return $backup;
            })
            ->addField('created', function($backup) {
                return $this->html->timeFromNow(\df\core\time\Date::fromCompressedString(substr($backup, 5, -4), 'UTC'));
            });
    }

    protected function _getMainButtonText() {
        return $this->_('Restore');
    }

    protected function _getMainButtonIcon() {
        return 'import';
    }

    protected function _apply() {
        $task = 'axis/restore-backup?backup='.basename($this->_file);
        return $this->task->initiateStream($task);
    }
}