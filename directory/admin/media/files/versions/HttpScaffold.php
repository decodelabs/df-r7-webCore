<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\media\files\versions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;

class HttpScaffold extends arch\scaffold\RecordAdmin {

    const TITLE = 'File versions';
    const ICON = 'list';
    const ADAPTER = 'axis://media/Version';

    const CAN_ADD = false;
    const CAN_DELETE = false;

    const LIST_FIELDS = [
        'number', 'fileName', 'file', 'fileSize', 'contentType', 'hash',
        'owner', 'purgeDate', 'creationDate'
    ];

    const DETAILS_FIELDS = [
        'number', 'fileName', 'fileSize', 'contentType', 'hash',
        'file', 'owner', 'purgeDate', 'creationDate'
    ];

// Record data
    protected function prepareRecordList($query, $mode) {
        $query
            ->importRelationBlock('file', 'link', ['activeVersion'])
            ->importRelationBlock('owner', 'link')
            ->paginate()
                ->setDefaultOrder('file ASC', 'number DESC');
    }

// Sections
    public function renderDetailsSectionBody($version) {
        $output = parent::renderDetailsSectionBody($version);

        if($date = $version['purgeDate']) {
            $output = [
                $this->html->flashMessage($this->_(
                    'This version item has been purged and the data is no longer available'
                ), 'warning'),

                $output
            ];
        }

        return $output;
    }

    public function renderFilesSectionBody($version) {
        return $this->apex->scaffold('../')
            ->renderRecordList(
                $version->files->select()
            );
    }


// Components
    public function getRecordOperativeLinks($version, $mode) {
        $isPurged = (bool)$version['purgeDate'];

        return [
            // Activate
            $this->apex->component('VersionLink', $version, $this->_('Activate'))
                ->setNode('activate')
                ->setDisposition('positive')
                ->setIcon('accept')
                ->isDisabled($version['isActive'] || $isPurged),

            // Edit
            $this->apex->component('VersionLink', $version, $this->_('Edit'))
                ->setNode('edit')
                ->setIcon('edit')
                ->isDisabled($isPurged),

            // Purge
            $this->apex->component('VersionLink', $version, $this->_('Purge'))
                ->setNode('purge')
                ->setIcon('delete')
                ->setDisposition('negative')
                ->isDisabled($version['isActive'] || $isPurged),
        ];
    }

    public function addIndexTransitiveLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link('../', $this->_('All files'))
                ->setIcon('file')
                ->setDisposition('transitive')
        );
    }

    protected function getParentSectionRequest() {
        return '../versions?file='.$this->getRecord()['#file'];
    }


// Fields
    public function defineNumberField($list, $mode) {
        $list->addField('number', '#');
    }

    public function overrideFileNameField($list, $mode) {
        $list->addField('fileName', function($version) {
            return $this->html->link(
                    $this->media->getVersionDownloadUrl(
                        $this->data->getRelationId($version, 'file'),
                        $version['id'],
                        $version['isActive']
                    ),
                    $version['fileName']
                )
                ->setIcon('download')
                ->setDisposition('external');
        });
    }

    public function defineFileSizeField($list, $mode) {
        $list->addField('fileSize', $this->_('Size'), function($version) {
            return $this->format->fileSize($version['fileSize']);
        });
    }

    public function defineHashField($list, $mode) {
        $list->addField('hash', function($version) {
            if($hash = $this->format->binHex($version['hash'])) {
                return $this->html('samp', $hash);
            }
        });
    }

    public function defineOwnerField($list, $mode) {
        $list->addField('owner', function($version) {
            return $this->apex->component('~admin/users/clients/UserLink', $version['owner'])
                ->isNullable(true);
        });
    }

    public function definePurgeDateField($list, $mode) {
        $list->addField('purgeDate', $this->_('Purged'), function($version, $context) {
            if($version['isActive']) {
                $context->rowTag->addClass('active');
            }

            if(!$date = $version['purgeDate']) {
                return;
            }

            $context->rowTag->addClass('disabled');
            return $this->html->timeFromNow($date);
        });
    }

    public function defineFileField($list, $mode) {
        $list->addField('file', function($version) {
            return $this->apex->component('../FileLink', $version['file']);
        });
    }
}