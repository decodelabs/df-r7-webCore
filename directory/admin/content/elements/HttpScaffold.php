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

    const SECTIONS = [
        'details',
        'history'
    ];

    const LIST_FIELDS = [
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

    public function renderHistorySectionBody($element) {
        $historyList = $this->data->content->history->fetchFor($element)
            ->paginateWith($this->request->query);

        return $this->apex->component('~admin/content/history/HistoryList')
            ->setCollection($historyList);
    }
}