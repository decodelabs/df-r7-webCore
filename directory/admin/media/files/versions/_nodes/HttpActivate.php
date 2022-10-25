<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\media\files\versions\_nodes;

use df\arch;

use DecodeLabs\Tagged as Html;
use DecodeLabs\Exceptional;

class HttpActivate extends arch\node\Form
{
    public const DEFAULT_EVENT = 'activate';

    protected $_version;

    protected function init(): void
    {
        $this->_version = $this->scaffold->getRecord();

        if ($this->_version['purgeDate']) {
            throw Exceptional::Forbidden([
                'message' => 'Purged versions cannot be activated',
                'http' => 403
            ]);
        }
    }

    protected function getInstanceId(): ?string
    {
        return $this->_version['id'];
    }

    protected function createUi(): void
    {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Activate'));

        $fs->push(
            Html::raw('<p>'.$this->_('Are you sure you want to activate this version?').'</p>'),

            $this->apex->component('VersionDetails', null, $this->_version),

            $this->html->yesNoButtonGroup('activate')
        );
    }

    protected function onActivateEvent()
    {
        return $this->complete(function () {
            $this->data->media->activateVersion($this->_version['file'], $this->_version);

            $this->comms->flashSuccess(
                'version.activate',
                $this->_('The file version has been successfully activated')
            );
        });
    }
}
