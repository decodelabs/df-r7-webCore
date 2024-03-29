<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\content\history\_components;

use DecodeLabs\Metamorph;

use DecodeLabs\Tagged as Html;
use df\arch;

class HistoryList extends arch\component\CollectionList
{
    protected $fields = [
        'user' => true,
        'action' => true,
        'description' => true,
        'date' => true,
        'actions' => true
    ];

    // User
    public function addUserField($list)
    {
        $this->setErrorMessage($this->_('There is no history to display'));

        $list->addField('user', function ($history) {
            return $this->apex->component('~admin/users/clients/UserLink', $history['user']);
        });
    }

    // Description
    public function addDescriptionField($list)
    {
        $list->addField('description', function ($history) {
            return Metamorph::idiom($history['description']);
        });
    }

    // Date
    public function addDatefield($list)
    {
        $list->addField('date', $this->_('When'), function ($history) {
            return $this->html->_('%t% ago', ['%t%' => Html::$time->since($history['date'])]);
        });
    }

    // Actions
    public function addActionsField($list)
    {
        $list->addField('actions', function ($history) {
            return $this->html->link(
                $this->uri('~admin/content/history/delete?history=' . $history['id'], true),
                $this->_('Delete')
            )
                ->setIcon('delete');
        });
    }
}
