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

class HttpScaffold extends arch\scaffold\template\RecordAdmin {
    
    const DIRECTORY_TITLE = 'User deactivations';
    const DIRECTORY_ICON = 'remove';
    const RECORD_ADAPTER = 'axis://user/ClientDeactivation';
    const RECORD_KEY_NAME = 'deactivation';
    const RECORD_NAME_KEY = 'date';

    const CAN_ADD_RECORD = false;
    const CAN_EDIT_RECORD = false;

    protected $_recordListFields = [
        'date' => true,
        'user' => true,
        'reason' => true,
        'actions' => true
    ];

    protected $_recordDetailsFields = [
        'user' => true,
        'date' => true,
        'reason' => true,
        'comments' => true
    ];

// Record data
    protected function _prepareRecordListQuery(opal\query\ISelectQuery $query, $mode) {
        $query->importRelationBlock('user', 'link');
    }

    public function applyRecordQuerySearch(opal\query\ISelectQuery $query, $search, $mode) {
        $query->beginWhereClause()
            ->whereCorrelation('user', 'in', 'id')
                ->from('axis://user/Client', 'client')
                ->where('client.fullName', 'matches', $search)
                ->orWhere('client.nickName', 'matches', $search)
                ->orWhere('client.email', 'matches', $search)
                ->endCorrelation()
            ->endClause();
    }

// Components
    public function addIndexHeaderBarTransitiveLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link(
                    '~admin/users/clients/',
                    $this->_('All users')
                )
                ->setIcon('user')
                ->setDisposition('transitive')
        );
    }


// Fields
    public function describeCommentsField($list) {
        $list->addField('comments', function($deactivation) {
            return $this->html->plainText($deactivation['comments']);
        });
    }
}