<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\consents;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    const TITLE = 'Cookie consent';
    const ICON = 'accept';
    const ADAPTER = 'axis://cookie/Consent';

    const LIST_FIELDS = [
        'id', 'creationDate', 'preferences', 'statistics', 'marketing'
    ];

    const CAN_ADD = false;
    const CAN_EDIT = false;


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
