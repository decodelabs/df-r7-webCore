<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\shared\media\files\_components;

use df;
use df\core;
use df\apex;
use df\arch;

class FileList extends arch\component\CollectionList {

    protected $_fields = [
        'fileName' => true,
        'bucket' => true,
        'fileSize' => true,
        'owner' => true,
        'creationDate' => true,
        'version' => true
    ];


// FileName
    public function addFileNameField($list) {
        $list->addField('fileName', $this->_('Name'));
    }

// Bucket
    public function addBucketField($list) {
        $list->addField('bucket', function($file) {
            return $file['bucket']['name'];
        });
    }

// Size
    public function addFileSizeField($list) {
        $list->addField('fileSize', function($file) {
            return $this->format->fileSize($file['fileSize']);
        });
    }

// Owner
    public function addOwnerField($list) {
        $list->addField('owner', function($file) {
            return $file['owner']['fullName'];
        });
    }

// Created
    public function addCreationDateField($list) {
        $list->addField('creationDate', $this->_('Created'), function($file) {
            return $this->html->timeFromNow($file['creationDate']);
        });
    }

// Version
    public function addVersionField($list) {
        $list->addField('version', $this->_('Ver'));
    }
}