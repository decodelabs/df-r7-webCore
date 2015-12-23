<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\elements;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;

class HttpScaffold extends arch\scaffold\RecordAdmin {

    const TITLE = 'Elements';
    const ICON = 'element';
    const ADAPTER = 'axis://content/Element';
    const NAME_FIELD = 'slug';

    protected $_sections = [
        'details',
        'history'
    ];

    protected $_recordListFields = [
        'slug', 'name', 'owner', 'creationDate',
        'lastEditDate'
    ];

// Record data
    protected function prepareRecordList($query, $mode) {
        $query->importRelationBlock('owner', 'link');
    }

    protected function countSectionItems($record) {
        return [
            'history' => $this->data->content->history->countFor($record)
        ];
    }

// Sections
    public function renderDetailsSectionBody($element) {
        return [
            parent::renderDetailsSectionBody($element),

            $this->html('h3', $this->_('Body')),
            $this->nightfire->renderSlot($element['body'])
        ];
    }

    public function renderHistorySectionBody($task) {
        $historyList = $this->data->content->history->fetchFor($task)
            ->paginateWith($this->request->query);

        return $this->apex->component('~admin/content/history/HistoryList')
            ->setCollection($historyList);
    }
}