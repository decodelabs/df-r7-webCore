<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\elements;

use DecodeLabs\Tagged as Html;

use df\arch;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    public const TITLE = 'Elements';
    public const ICON = 'element';
    public const ADAPTER = 'axis://content/Element';
    public const NAME_FIELD = 'slug';
    public const IS_SHARED = true;

    public const SECTIONS = [
        'details',
        'history'
    ];

    public const LIST_FIELDS = [
        'slug', 'name', 'owner', 'creationDate',
        'lastEditDate'
    ];

    public const CAN_SELECT = true;
    public const CONFIRM_DELETE = true;

    // Record data
    protected function prepareRecordList($query, $mode)
    {
        $query->importRelationBlock('owner', 'link');
    }

    protected function countSectionItems($record): array
    {
        return [
            'history' => $this->data->content->history->countFor($record)
        ];
    }

    // Sections
    public function renderDetailsSectionBody($element)
    {
        return [
            parent::renderDetailsSectionBody($element),

            Html::{'h3'}($this->_('Body')),
            $this->nightfire->renderSlot($element['body'])
        ];
    }

    public function renderHistorySectionBody($element)
    {
        $historyList = $this->data->content->history->fetchFor($element)
            ->paginateWith($this->request->query);

        return $this->apex->component('~admin/content/history/HistoryList')
            ->setCollection($historyList);
    }
}
