<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\media;

use DecodeLabs\Tagged as Html;

use df\arch;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    public const TITLE = 'Media buckets';
    public const ICON = 'database';
    public const ADAPTER = 'axis://media/Bucket';
    public const DEFAULT_SECTION = 'files';
    public const IS_PARENT = true;

    public const SECTIONS = [
        'details',
        'files' => 'file'
    ];

    public const LIST_FIELDS = [
        'name', 'slug', 'files', 'size'
    ];

    public const CONFIRM_DELETE = true;


    // Record data
    protected function prepareRecordList($query, $mode)
    {
        $query
            ->countRelation('files')
            ->correlate('SUM(fileSize)', 'size')
                ->from('axis://media/Version', 'version')
                ->whereCorrelation('id', 'in', 'activeVersion')
                    ->from('axis://media/File', 'file')
                    ->on('file.bucket', '=', 'bucket.id')
                    ->endCorrelation()
                ->endCorrelation();
    }


    // Filters
    protected function generateRecordSwitchers(): iterable
    {
        yield $this->newRecordSwitcher(function () {
            yield from $this->getRecordAdapter()->select('id', 'name')
                ->orderBy('name')
                ->toList('id', 'name');
        });
    }


    // Sections
    public function renderFilesSectionBody($bucket)
    {
        return $this->apex->scaffold('./files/')
            ->renderRecordList(function ($query) use ($bucket) {
                $query->where('bucket', '=', $bucket['id']);
            }, [
                'bucket' => false
            ], 'bucket');
    }

    // Components
    public function generateIndexTransitiveLinks(): iterable
    {
        yield 'files' => $this->html->link('./files/', $this->_('All files'))
            ->setIcon('file')
            ->setDisposition('transitive');
    }

    public function generateFilesSectionTransitiveLinks(): iterable
    {
        yield from $this->generateIndexTransitiveLinks();
    }

    public function generateFilesSectionSubOperativeLinks(): iterable
    {
        yield 'add' => $this->html->link(
            $this->uri('./files/add?bucket=' . $this->getRecordId(), true),
            $this->_('Add file')
        )
            ->setIcon('add');
    }


    // Fields
    public function defineFilesField($list, $mode)
    {
        $list->addField('files', function ($bucket) {
            return $this->html->link('./files?bucket=' . $bucket['id'], $bucket['files'])
                ->setIcon('file');
        });
    }

    public function defineSizeField($list, $mode)
    {
        $list->addField('size', function ($bucket) {
            return Html::$number->fileSize((int)$bucket['size']);
        });
    }
}
