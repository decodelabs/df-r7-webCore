<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\media\files\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpAdd extends arch\node\Form {

    protected $_file;

    protected function init() {
        $this->_file = $this->scaffold->newRecord();
    }

    protected function loadDelegates() {
        $this->loadDelegate('upload', '../TempUploader')
            ->isForOne(true)
            ->isRequired($this->_file->isNew());

        $this->loadDelegate('bucket', '../BucketSelector')
            ->isForOne(true)
            ->isRequired(true);

        $this->loadDelegate('owner', '~admin/users/clients/UserSelector')
            ->isForOne(true)
            ->isRequired(false);
    }

    protected function setDefaultValues() {
        $this['owner']->setSelected(
            $this->user->client->getId()
        );
    }

    protected function createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('File details'));

        $this['upload']->renderContainerContent($fs);

        if(!$this->_file->isNew()) {
            $fs[0]->push(
                $this->html('span', [
                    $this->html->checkbox('overwriteName', $this->values->overwriteName, $this->_(
                        'Use file name of new version'
                    ))
                ], ['style' => 'padding-left: 3em;'])
            );
        }

        // File name
        $fa = $fs->addField($this->_('File name'))->push(
            $this->html->textbox('fileName', $this->values->fileName)
                ->isRequired(!$this->_file->isNew())
                ->setMaxLength(1024)
        );

        if($this->_file->isNew()) {
            $fa->setDescription($this->_('Leave empty to fill from uploaded file'));
        }

        // Bucket
        $fs->addField($this->_('Bucket'))->push($this['bucket']);

        // Owner
        $fs->addField($this->_('Owner'))->push($this['owner']);

        // Buttons
        $fs->addDefaultButtonGroup();
    }

    protected function onSaveEvent() {
        $filePath = $this['upload']->apply();

        if($filePath && $this->values['overwriteName']) {
            unset($this->values->fileName);
        }

        $validator = $this->data->newValidator()

            // Bucket
            ->addField('bucket', 'delegate')
                ->fromForm($this)

            // File name
            ->addRequiredField('fileName', 'text')
                ->setMaxLength(1024)
                ->setSanitizer(function($value) use($filePath) {
                    if(!strlen($value)) {
                        $value = basename($filePath);
                    }

                    return $value;
                })

            // Owner
            ->addField('owner', 'delegate')
                ->fromForm($this)

            ->validate($this->values)
            ->applyTo($this->_file);

        return $this->complete(function() use($validator, $filePath) {
            if($this->_file->isNew()) {
                $this->data->media->publishFile(
                    $filePath,
                    $this->data->media->bucket->fetchByPrimary($validator['bucket']),
                    $validator->getValues()
                );
            } else if($filePath !== null) {
                $this->data->media->publishVersion(
                    $this->_file,
                    $filePath,
                    $validator->getValues()
                );
            } else {
                $this->_file->save();
            }

            $this->comms->flashSaveSuccess('file');
        });
    }
}