<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\media\files\versions\_nodes;

use df\arch;

use DecodeLabs\Tagged as Html;

class HttpPurge extends arch\node\Form
{
    public const DEFAULT_EVENT = 'purge';

    protected $_version;

    protected function init(): void
    {
        $this->_version = $this->scaffold->getRecord();
    }

    protected function getInstanceId(): ?string
    {
        return $this->_version['id'];
    }

    protected function createUi(): void
    {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Purge'));

        $fs->push(
            Html::raw('<p>'.$this->_('Are you sure you want to purge this version? The data uploaded for this version will be deleted and cannot be restored!').'</p>'),

            $this->apex->component('VersionDetails', null, $this->_version),

            $this->html->yesNoButtonGroup('purge')
        );
    }

    protected function onPurgeEvent()
    {
        return $this->complete(function () {
            $this->data->media->purgeVersion($this->_version);

            $this->comms->flashSuccess(
                'version.purge',
                $this->_('The file version has been successfully purged')
            );
        });
    }
}
