<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\system\pestControl\reports;

use DecodeLabs\Tagged as Html;

use df\arch;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    public const TITLE = 'HTTP reports';
    public const ICON = 'report';
    public const ADAPTER = 'axis://pestControl/Report';
    public const NAME_FIELD = 'date';
    public const CAN_ADD = false;
    public const CAN_EDIT = false;

    public const LIST_FIELDS = [
        'date', 'type', 'user', 'isProduction'
    ];

    public const DETAILS_FIELDS = [
        'date', 'type', 'userAgent', 'user', 'isProduction',
        'body'
    ];

    public const CAN_SELECT = true;


    // Record data
    protected function prepareRecordList($query, $mode)
    {
        $query
            ->importRelationBlock('user', 'link')
        ;
    }


    // Components
    public function generateIndexOperativeLinks(): iterable
    {
        yield 'purge' => $this->html->link($this->uri('./purge', true), $this->_('Purge old logs'))
            ->setIcon('delete');

        yield 'purgeAll' => $this->html->link($this->uri('./purge-all', true), $this->_('Purge ALL'))
            ->setIcon('delete');
    }


    // Fields
    public function defineUserAgentField($list, $mode)
    {
        $list->addField('userAgent', function ($report) {
            if ($agent = $report['userAgent']) {
                return Html::{'code'}($agent['body']);
            }
        });
    }

    public function defineUserField($list, $mode)
    {
        $list->addField('user', function ($report) {
            return $this->apex->component('~admin/users/clients/UserLink', $report['user'])
                ->isNullable(true);
        });
    }

    public function defineIsProductionField($list, $mode)
    {
        $list->addField('isProduction', $mode == 'list' ? $this->_('Prod') : $this->_('Production'), function ($report, $context) {
            if (!$report['isProduction']) {
                $context->getRowTag()->addClass('inactive');
            }

            return $this->html->booleanIcon($report['isProduction']);
        });
    }

    public function defineBodyField($list, $mode)
    {
        $list->addField('body', function ($report) {
            return $this->html->attributeList($report['body']);
        });
    }
}
