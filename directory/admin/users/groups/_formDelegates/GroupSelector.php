<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\groups\_formDelegates;

use df;
use df\core;
use df\arch;

class GroupSelector extends arch\form\template\SearchSelectorDelegate {
    
    protected function _fetchResultList(array $ids) {
        return $this->data->user->group->fetch()
            ->countRelation('users')
            ->countRelation('roles')
            ->where('id', 'in', $ids)
            ->chain([$this, 'applyDependencies']);
    }

    protected function _getSearchResultIdList($search, array $selected) {
        return $this->data->user->group->select('id')
            ->where('name', 'matches', $search)
            ->where('id', '!in', $selected)
            ->chain([$this, 'applyDependencies'])
            ->toList('id');
    }

    protected function _renderCollectionList($result) {
        return $this->import->component('GroupList', '~admin/users/groups/', [
                'actions' => false
            ])
            ->setCollection($result);
    }
}
