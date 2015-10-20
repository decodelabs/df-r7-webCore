<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\media\files\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpAdd extends arch\form\Action {

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
        $this->getDelegate('owner')->setSelected(
            $this->user->client->getId()
        );
    }

    protected function createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('File details'));

        $this->getDelegate('upload')->renderContainerContent($fs);

        if(!$this->_file->isNew()) {
            $fs[0]->push(
                $this->html('span', [
                    $this->html->checkbox('overwriteName', $this->values->overwriteName, $this->_(
                        'Use file name of new version'
                    ))
                ], ['style' => 'padding-left: 3em;'])
            );
        }

        // Slug
        $fs->addFieldArea($this->_('Slug'))->push(
            $this->html->textbox('slug', $this->values->slug)
                ->setPlaceholder($this->_('Auto-generate from file name'))
        );

        // File name
        $fa = $fs->addFieldArea($this->_('File name'))->push(
            $this->html->textbox('fileName', $this->values->fileName)
                ->isRequired(!$this->_file->isNew())
                ->setMaxLength(1024)
        );

        if($this->_file->isNew()) {
            $fa->setDescription($this->_('Leave empty to fill from uploaded file'));
        }

        // Bucket
        $fs->addFieldArea($this->_('Bucket'))->push(
            $this->getDelegate('bucket')
        );

        // Owner
        $fs->addFieldArea($this->_('Owner'))->push(
            $this->getDelegate('owner')
        );

        // Buttons
        $fs->addDefaultButtonGroup();
    }

    protected function onSaveEvent() {
        $filePath = $this->getDelegate('upload')->apply();

        if($filePath && $this->values['overwriteName']) {
            unset($this->values->fileName, $this->values->slug);
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

            // Slug
            ->addRequiredField('slug')
                ->setDefaultValueField('fileName', function($fileName) {
                    return (new core\uri\Path($fileName))->getFilename();
                })
                ->allowPathFormat(true)
                ->allowRoot(false)
                ->setRecord($this->_file)
                ->addFilter(function($clause, $field) {
                    $clause->where('bucket', '=', $field->validator['bucket']);
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