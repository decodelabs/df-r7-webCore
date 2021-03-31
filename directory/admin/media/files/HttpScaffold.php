<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\media\files;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;

use DecodeLabs\Tagged as Html;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    const TITLE = 'Files';
    const ICON = 'file';
    const ADAPTER = 'axis://media/File';
    const IS_SHARED = true;

    const SECTIONS = [
        'details',
        'versions' => 'list'
    ];

    const LIST_FIELDS = [
        'thumbnail', 'fileName', 'bucket', 'fileSize', 'owner',
        'creationDate', 'version'
    ];

    const DETAILS_FIELDS = [
        'url', 'bucket', 'fileName', 'fileSize', 'owner',
        'hash', 'creationDate'
    ];

    const CAN_PREVIEW = true;
    const CAN_SELECT = true;

    // Record data
    protected function prepareRecordList($query, $mode)
    {
        $query
            ->countRelation('versions')
            ->leftJoinRelation('activeVersion', 'number as version', 'fileSize', 'contentType')
            ->importRelationBlock('bucket', 'link')
            ->importRelationBlock('owner', 'link')
            ->paginate()
                ->addOrderableFields('version')
                ->end();
    }

    public function deleteRecord(opal\record\IRecord $file, array $flags=[])
    {
        $this->data->media->deleteFile($file);
        return $this;
    }



    // Filters
    protected function generateRecordFilters(): iterable
    {
        yield $this->newRecordFilter('bucket', 'All buckets', function () {
            yield from $this->data->media->bucket->select('id', 'name')
                ->orderBy('name')
                ->toList('id', 'name');
        });
    }


    // Sections
    public function renderDetailsSectionBody($file)
    {
        return $this->html->panelSet()
            ->addPanel(parent::renderDetailsSectionBody($file))
            ->addPanel(function () use ($file) {
                if (false !== strpos($file['activeVersion']['contentType'], 'image/')) {
                    $image = $this->html->image($file->getImageUrl('[rs:380|380|f]'));

                    if ($file['activeVersion']['contentType'] == 'image/svg+xml') {
                        $image->setStyle('height', '25em');
                    }

                    return $image;
                }
            });
    }

    public function renderVersionsSectionBody($file)
    {
        return $this->apex->scaffold('./versions/')
            ->renderRecordList(function ($query) use ($file) {
                $query->where('file', '=', $file['id']);
            }, [
                'file' => false
            ]);
    }


    // Components
    protected function getRecordParentUriString(array $file): ?string
    {
        return '../files?bucket='.$this->data->getRelationId($file, 'bucket');
    }

    protected function getRecordPreviewUriString(array $file): string
    {
        return 'media/download?file='.$file['id'];
    }


    // Fields
    public function defineThumbnailField($list, $mode)
    {
        $list->addField('thumbnail', $this->_('Thumb'), function ($file) {
            if (false !== strpos($file['contentType'], 'image/')) {
                $image = $this->media->image($file['id'], '[rs:80|80|f]', null, 80);

                if ($file['activeVersion']['contentType'] == 'image/svg+xml') {
                    $image->setStyle('height', '6em');
                }

                return $image;
            }
        });
    }

    public function defineBucketField($list, $mode)
    {
        $list->addField('bucket', function ($file) {
            return $this->apex->component('../BucketLink', $file['bucket']);
        });
    }

    public function defineUrlField($list, $mode)
    {
        $list->addField('url', $this->_('Copy & paste url'), function ($file) {
            return Html::{'code'}('media/download/f'.$file['id']);
        });
    }

    public function defineFileSizeField($list, $mode)
    {
        $list->addField('fileSize', $this->_('Size'), function ($file) use ($mode) {
            if ($mode == 'list') {
                return Html::$number->fileSize($file['fileSize']);
            } else {
                return Html::$number->fileSize($file['activeVersion']['fileSize']);
            }
        });
    }

    public function defineHashField($list, $mode)
    {
        $list->addField('hash', function ($file) {
            if ($hash = $this->format->binHex($file['activeVersion']['hash'])) {
                return Html::{'samp'}($hash);
            }
        });
    }

    public function defineOwnerField($list, $mode)
    {
        $list->addField('owner', function ($file) {
            return $this->apex->component('~admin/users/clients/UserLink', $file['owner'])
                ->isNullable(true);
        });
    }

    public function defineVersionField($list, $mode)
    {
        if ($mode == 'list') {
            $list->addField('version', $this->_('Ver.'), function ($file) {
                if ($file['versions'] != $file['version']) {
                    return $this->html->_('<strong class="warning">%v%</strong> of <strong>%t%</strong>', [
                        '%v%' => $file['version'],
                        '%t%' => $file['versions']
                    ]);
                } else {
                    return Html::{'strong'}($file['version']);
                }
            });
        } else {
            return false;
        }
    }
}
