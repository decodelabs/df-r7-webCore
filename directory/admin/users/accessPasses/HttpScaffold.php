<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\accessPasses;

use df;
use df\core;
use df\apex;
use df\arch;

use DecodeLabs\Tagged\Html;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    const TITLE = 'Access passes';
    const ICON = 'key';
    const ADAPTER = 'axis://user/AccessPass';
    const KEY_NAME = 'pass';

    const CAN_EDIT = false;

    const LIST_FIELDS = [
        'id', 'user', 'creationDate', 'expiryDate'
    ];


    // Record data
    protected function prepareRecordList($query, $mode)
    {
        $query
            ->importRelationBlock('user', 'link');
    }


    // Components
    public function getRecordOperativeLinks($pass, $mode)
    {
        return [
            // Consume
            $this->html->link('account/access-pass?pass='.$pass['id'], 'Consume')
                ->setIcon('user')
                ->setDisposition('positive'),

            parent::getRecordOperativeLinks($pass, $mode)
        ];
    }


    // Fields
    public function defineExpiryDateField($list, $mode)
    {
        $list->addField('expiryDate', $this->_('Expires'), function ($pass, $context) {
            if ($pass['expiryDate']->isPast()) {
                $context->rowTag->addClass('inactive');
            } else {
                $context->cellTag->addClass('positive');
            }

            return Html::$time->since($pass['expiryDate']);
        });
    }
}
