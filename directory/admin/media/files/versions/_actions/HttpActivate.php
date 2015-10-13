<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\media\files\versions\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpActivate extends arch\form\Action {
    
    const DEFAULT_EVENT = 'activate';

    protected $_version;

    protected function init() {
        $this->_version = $this->scaffold->getRecord();

        if($this->_version['purgeDate']) {
            $this->throwError(403, 'Purged versions cannot be activated');
        }
    }

    protected function getInstanceId() {
        return $this->_version['id'];
    }

    protected function createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Activate'));

        $fs->push(
            $this->html('<p>'.$this->_('Are you sure you want to activate this version?').'</p>'),

            $this->apex->component('VersionDetails', null, $this->_version),

            $this->html->yesNoButtonGroup('activate')
        );
    }

    protected function onActivateEvent() {
        return $this->complete(function() {
            $this->data->media->activateVersion($this->_version['file'], $this->_version);

            $this->comms->flashSuccess(
                'version.activate',
                $this->_('The file version has been successfully activated')
            );
        });
    }
}