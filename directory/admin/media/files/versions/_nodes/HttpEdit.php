<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\media\files\versions\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpEdit extends arch\node\Form {

    protected $_version;

    protected function init() {
        $this->_version = $this->scaffold->getRecord();
    }

    protected function getInstanceId() {
        return $this->_version['id'];
    }

    protected function setDefaultValues() {
        $this->values->importFrom($this->_version, [
            'fileName', 'contentType'
        ]);
    }

    protected function createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Version details'));

        // Filename
        $fs->addField($this->_('File name'))->push(
            $this->html->textbox('fileName', $this->values->fileName)
                ->isRequired(true)
                ->setMaxLength(1024)
        );

        // Content type
        $fs->addField($this->_('Content type'))->push(
            $this->html->textbox('contentType', $this->values->contentType)
                ->isRequired(true)
                ->setMaxLength(128)
        );

        // Buttons
        $fs->addDefaultButtonGroup();
    }

    protected function onSaveEvent() {
        $this->data->newValidator()

            // Filename
            ->addRequiredField('fileName', 'text')
                ->setMaxLength(1024)

            // Content type
            ->addRequiredField('contentType', 'text')
                ->setPattern('/^[a-zA-Z0-9\-_]+\/[a-zA-Z0-9\-_]+$/')

            ->validate($this->values)
            ->applyTo($this->_version);

        return $this->complete(function() {
            $this->_version->save();
            $this->comms->flashSaveSuccess('version');
        });
    }
}