<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\accessPasses;

use DecodeLabs\Tagged as Html;

use df\arch;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    public const TITLE = 'Access passes';
    public const ICON = 'key';
    public const ADAPTER = 'axis://user/AccessPass';
    public const KEY_NAME = 'pass';

    public const CAN_EDIT = false;

    public const LIST_FIELDS = [
        'id', 'user', 'creationDate', 'expiryDate'
    ];


    // Record data
    protected function prepareRecordList($query, $mode)
    {
        $query
            ->importRelationBlock('user', 'link');
    }


    // Components
    public function generateRecordOperativeLinks(array $pass): iterable
    {
        // Consume
        yield 'consume' => $this->html->link('account/access-pass?pass=' . $pass['id'], 'Consume')
            ->setIcon('user')
            ->setDisposition('positive');

        yield from parent::generateRecordOperativeLinks($pass);
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
