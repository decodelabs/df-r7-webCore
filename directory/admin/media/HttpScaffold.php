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

class HttpScaffold extends arch\scaffold\template\RecordAdmin {
    
    const DIRECTORY_TITLE = 'Media buckets';
    const DIRECTORY_ICON = 'database';
    const RECORD_ADAPTER = 'axis://media/Bucket';
    const DEFAULT_RECORD_ACTION = 'files';

    protected $_sections = [
        'details',
        'files' => 'file'
    ];

    protected $_recordListFields = [
        'name', 'slug', 'context1', 'context2', 'files',
    ];

// Record data
    protected function prepareRecordList($query, $mode) {
        $query
            ->countRelation('files');
    }

// Sections
    public function renderFilesSectionBody($bucket) {
        return $this->apex->scaffold('./files/')
            ->renderRecordList(
                $bucket->files->select(),
                ['bucket' => false]
            );
    }

// Components
    public function addIndexTransitiveLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link('./files/', $this->_('All files'))
                ->setIcon('file')
                ->setDisposition('transitive')
        );  
    }

    public function addFilesSectionTransitiveLinks($menu, $bar) {
        $this->addIndexTransitiveLinks($menu, $bar);
    }

    public function addFilesSectionSubOperativeLinks($menu, $bar) {
        $bucket = $this->getRecord();

        $menu->addLinks(
            $this->html->link(
                    $this->uri('./files/add?bucket='.$bucket['id'], true), 
                    $this->_('Add file')
                )
                ->setIcon('add')
        );
    }
};