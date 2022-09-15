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

use DecodeLabs\Disciple;

class HttpAdd extends arch\node\Form
{
    protected $_file;

    protected function init()
    {
        $this->_file = $this->scaffold->newRecord();
    }

    protected function loadDelegates()
    {
        /**
         * File
         * @var apex\directory\shared\media\_formDelegates\CustomTempUploader $file
         */
        $file = $this->loadDelegate('upload', '../CustomTempUploader');
        $file
            ->isForOne(true)
            ->isRequired($this->_file->isNew())
            ->shouldShowUploadButton(true);

        /**
         * Bucket
         * @var arch\scaffold\Node\Form\SelectorDelegate $bucket
         */
        $bucket = $this->loadDelegate('bucket', '../BucketSelector');
        $bucket
            ->isForOne(true)
            ->isRequired(true);

        /**
         * User
         * @var arch\scaffold\Node\Form\SelectorDelegate $user
         */
        $user = $this->loadDelegate('owner', '~admin/users/clients/UserSelector');
        $user
            ->isForOne(true)
            ->isRequired(false);
    }

    protected function setDefaultValues()
    {
        $this['owner']->setSelected(
            Disciple::getId()
        );
    }

    protected function createUi()
    {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('File details'));

        $fs->addField($this->_('File'))->push(
            $this['upload']
        );

        if (!$this->_file->isNew()) {
            $fs->addField()->push(
                $this->html->checkbox('overwriteName', $this->values->overwriteName, $this->_(
                    'Use file name of new version'
                ))
            );
        }

        // File name
        $fa = $fs->addField($this->_('File name'))->push(
            $this->html->textbox('fileName', $this->values->fileName)
                ->isRequired(!$this->_file->isNew())
                ->setMaxLength(1024)
        );

        if ($this->_file->isNew()) {
            $fa->setDescription($this->_('Leave empty to fill from uploaded file'));
        }

        // Bucket
        $fs->addField($this->_('Bucket'))->push($this['bucket']);

        // Owner
        $fs->addField($this->_('Owner'))->push($this['owner']);

        // Buttons
        $fs->addDefaultButtonGroup();
    }

    protected function onSaveEvent()
    {
        $filePath = $this['upload']->apply();

        if ($filePath && $this->values['overwriteName']) {
            unset($this->values->fileName);
        }

        $validator = $this->data->newValidator()

            // Bucket
            ->addField('bucket', 'delegate')
                ->fromForm($this)

            // File name
            ->addRequiredField('fileName', 'text')
                ->setMaxLength(1024)
                ->setSanitizer(function ($value) use ($filePath) {
                    if (!strlen($value)) {
                        $value = basename($filePath);
                    }

                    return $value;
                })

            // Owner
            ->addField('owner', 'delegate')
                ->fromForm($this)

            ->validate($this->values)
            ->applyTo($this->_file);

        return $this->complete(function () use ($validator, $filePath) {
            if ($this->_file->isNew()) {
                $this->data->media->publishFile(
                    $filePath,
                    $this->data->media->bucket->fetchByPrimary($validator['bucket']),
                    $validator->getValues()
                );
            } elseif ($filePath !== null) {
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
