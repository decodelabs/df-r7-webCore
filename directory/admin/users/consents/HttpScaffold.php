<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\consents;

use df\arch;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    public const TITLE = 'Cookie consent';
    public const ICON = 'accept';
    public const ADAPTER = 'axis://cookie/Consent';

    public const LIST_FIELDS = [
        'id', 'creationDate', 'preferences', 'statistics', 'marketing'
    ];

    public const CAN_ADD = false;
    public const CAN_EDIT = false;


    // Fields
    public function definePreferencesField($list, $mode)
    {
        $list->addField('preferences', function ($consent) {
            return $this->html->yesNoIcon($consent['preferences']);
        });
    }

    public function defineStatisticsField($list, $mode)
    {
        $list->addField('statistics', function ($consent) {
            return $this->html->yesNoIcon($consent['statistics']);
        });
    }

    public function defineMarketingField($list, $mode)
    {
        $list->addField('marketing', function ($consent) {
            return $this->html->yesNoIcon($consent['marketing']);
        });
    }
}
