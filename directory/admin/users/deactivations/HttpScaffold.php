<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\deactivations;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;

class HttpScaffold extends arch\scaffold\RecordAdmin {

    const TITLE = 'User deactivations';
    const ICON = 'remove';
    const ADAPTER = 'axis://user/ClientDeactivation';
    const KEY_NAME = 'deactivation';
    const NAME_FIELD = 'date';

    const CAN_ADD = false;
    const CAN_EDIT = false;

    protected $_recordListFields = [
        'date', 'user', 'reason'
    ];

    protected $_recordDetailsFields = [
        'user', 'date', 'reason', 'comments'
    ];

// Record data
    protected function prepareRecordList($query, $mode) {
        $query->importRelationBlock('user', 'link');
    }

    protected function searchRecordList($query, $search) {
        $query->searchFor($search, [
            'user|fullName' => 5
        ]);
    }

// Components
    public function addIndexTransitiveLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link('../clients/', $this->_('All users'))
                ->setIcon('user')
                ->setDisposition('transitive')
        );
    }


// Fields
    public function defineCommentsField($list) {
        $list->addField('comments', function($deactivation) {
            return $this->html->plainText($deactivation['comments']);
        });
    }
}