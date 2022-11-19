<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\pestControl\access;

use DecodeLabs\Dictum;

use DecodeLabs\Tagged as Html;
use df\arch;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    public const TITLE = 'Access errors';
    public const ICON = 'lock';
    public const ADAPTER = 'axis://pestControl/AccessLog';
    public const NAME_FIELD = 'date';
    public const KEY_NAME = 'log';
    public const CAN_ADD = false;
    public const CAN_EDIT = false;

    public const LIST_FIELDS = [
        'date', 'mode', 'code', 'request', 'message',
        'user', 'isProduction'
    ];

    public const DETAILS_FIELDS = [
        'date', 'mode', 'code', 'request', 'userAgent',
        'message', 'user', 'isProduction'
    ];

    public const CAN_SELECT = true;

    // Record data
    public function describeRecord($record)
    {
        return $record['mode'] . ' ' . $record['code'] . ' - ' . Dictum::$time->date($record['date']);
    }

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
    public function defineModeField($list, $mode)
    {
        $list->addField('mode', function ($log) {
            return Dictum::name($log['mode']);
        });
    }

    public function defineRequestField($list, $mode)
    {
        return $this->apex->scaffold('../')->defineRequestField($list, $mode);
    }

    public function defineMessageField($list, $mode)
    {
        $list->addField('message', function ($error) use ($mode) {
            $message = $error['message'];

            if ($mode == 'list') {
                $message = Dictum::shorten($message, 25);
            }

            $output = Html::{'samp'}($message);

            if ($mode == 'list') {
                $output->setTitle($error['message']);
            }

            return $output;
        });
    }

    public function defineUserAgentField($list, $mode)
    {
        $list->addField('userAgent', function ($log) {
            if ($agent = $log['userAgent']) {
                return Html::{'code'}($agent['body']);
            }
        });
    }

    public function defineUserField($list, $mode)
    {
        $list->addField('user', function ($log) {
            return $this->apex->component('~admin/users/clients/UserLink', $log['user'])
                ->isNullable(true);
        });
    }

    public function defineIsProductionField($list, $mode)
    {
        $list->addField('isProduction', $mode == 'list' ? $this->_('Prod') : $this->_('Production'), function ($log, $context) {
            if (!$log['isProduction']) {
                $context->getRowTag()->addClass('inactive');
            }

            return $this->html->booleanIcon($log['isProduction']);
        });
    }
}
