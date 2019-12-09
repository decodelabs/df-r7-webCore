<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\media;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;

use DecodeLabs\Tagged\Html;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    const TITLE = 'Media buckets';
    const ICON = 'database';
    const ADAPTER = 'axis://media/Bucket';
    const DEFAULT_SECTION = 'files';

    const SECTIONS = [
        'details',
        'files' => 'file'
    ];

    const LIST_FIELDS = [
        'name', 'slug', 'files', 'size'
    ];

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

    // Sections
    public function renderFilesSectionBody($bucket)
    {
        return $this->apex->scaffold('./files/')
            ->renderRecordList(
                $bucket->files->select(),
                ['bucket' => false]
            );
    }

    // Components
    public function addIndexTransitiveLinks($menu, $bar)
    {
        $menu->addLinks(
            $this->html->link('./files/', $this->_('All files'))
                ->setIcon('file')
                ->setDisposition('transitive')
        );
    }

    public function addFilesSectionTransitiveLinks($menu, $bar)
    {
        $this->addIndexTransitiveLinks($menu, $bar);
    }

    public function addFilesSectionSubOperativeLinks($menu, $bar)
    {
        $bucket = $this->getRecord();

        $menu->addLinks(
            $this->html->link(
                    $this->uri('./files/add?bucket='.$bucket['id'], true),
                    $this->_('Add file')
                )
                ->setIcon('add')
        );
    }


    // Fields
    public function defineFilesField($list, $mode)
    {
        $list->addField('files', function ($bucket) {
            return $this->html->link('./files?bucket='.$bucket['id'], $bucket['files'])
                ->setIcon('file');
        });
    }

    public function defineSizeField($list, $mode)
    {
        $list->addField('size', function ($bucket) {
            return Html::$number->fileSize((int)$bucket['size']);
        });
    }
};
