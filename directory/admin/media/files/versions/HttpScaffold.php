<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\media\files\versions;

use DecodeLabs\Tagged as Html;

use df\arch;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    public const TITLE = 'File versions';
    public const ICON = 'list';
    public const ADAPTER = 'axis://media/Version';

    public const CAN_ADD = false;
    public const CAN_DELETE = false;

    public const LIST_FIELDS = [
        'number', 'fileName', 'file', 'fileSize', 'contentType', 'hash',
        'owner', 'purgeDate', 'creationDate'
    ];

    public const DETAILS_FIELDS = [
        'number', 'fileName', 'fileSize', 'contentType', 'hash',
        'file', 'owner', 'purgeDate', 'creationDate'
    ];

    // Record data
    protected function prepareRecordList($query, $mode)
    {
        $query
            ->importRelationBlock('file', 'link', ['activeVersion'])
            ->importRelationBlock('owner', 'link')
            ->paginate()
                ->setDefaultOrder('file ASC', 'number DESC');
    }

    // Sections
    public function renderDetailsSectionBody($version)
    {
        $output = parent::renderDetailsSectionBody($version);

        if ($date = $version['purgeDate']) {
            $output = [
                $this->html->flashMessage($this->_(
                    'This version item has been purged and the data is no longer available'
                ), 'warning'),

                $output
            ];
        }

        return $output;
    }

    // Components
    public function generateRecordOperativeLinks(array $version): iterable
    {
        $isPurged = (bool)$version['purgeDate'];

        // Activate
        yield 'activate' => $this->apex->component('VersionLink', $version, $this->_('Activate'))
            ->setNode('activate')
            ->setDisposition('positive')
            ->setIcon('accept')
            ->isDisabled($version['isActive'] || $isPurged);

        // Edit
        yield 'edit' => $this->apex->component('VersionLink', $version, $this->_('Edit'))
            ->setNode('edit')
            ->setIcon('edit')
            ->isDisabled($isPurged);

        // Purge
        yield 'purge' => $this->apex->component('VersionLink', $version, $this->_('Purge'))
            ->setNode('purge')
            ->setIcon('delete')
            ->setDisposition('negative')
            ->isDisabled($version['isActive'] || $isPurged);
    }

    public function generateIndexTransitiveLinks(): iterable
    {
        yield 'all' => $this->html->link('../', $this->_('All files'))
            ->setIcon('file')
            ->setDisposition('transitive');
    }

    protected function getRecordParentUriString(array $version): ?string
    {
        return '../versions?file=' . $this->data->getRelationId($version, 'file');
    }


    // Fields
    public function defineNumberField($list, $mode)
    {
        $list->addField('number', '#');
    }

    public function overrideFileNameField($list, $mode)
    {
        $list->addField('fileName', function ($version) {
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

    public function defineFileSizeField($list, $mode)
    {
        $list->addField('fileSize', $this->_('Size'), function ($version) {
            return Html::$number->fileSize($version['fileSize']);
        });
    }

    public function defineHashField($list, $mode)
    {
        $list->addField('hash', function ($version) {
            if (null === ($hash = $version['hash'])) {
                return null;
            }

            return Html::{'samp'}(bin2hex($hash));
        });
    }

    public function defineOwnerField($list, $mode)
    {
        $list->addField('owner', function ($version) {
            return $this->apex->component('~admin/users/clients/UserLink', $version['owner'])
                ->isNullable(true);
        });
    }

    public function definePurgeDateField($list, $mode)
    {
        $list->addField('purgeDate', $this->_('Purged'), function ($version, $context) {
            if ($version['isActive']) {
                $context->rowTag->addClass('active');
            }

            if (!$date = $version['purgeDate']) {
                return;
            }

            $context->rowTag->addClass('disabled');
            return Html::$time->since($date);
        });
    }

    public function defineFileField($list, $mode)
    {
        $list->addField('file', function ($version) {
            return $this->apex->component('../FileLink', $version['file']);
        });
    }
}
