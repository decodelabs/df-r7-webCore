<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\roles\_formDelegates;

use df;
use df\core;
use df\arch;

class RoleSelector extends arch\form\template\SearchSelectorDelegate {
    
    protected function _fetchResultList(array $ids) {
        return $this->data->user->role->fetch()
            ->countRelation('groups')
            ->countRelation('keys')
            ->where('id', 'in', $ids)
            ->chain([$this, 'applyDependencies'])
            ->orderBy('name');
    }

    protected function _getSearchResultIdList($search, array $selected) {
        return $this->data->user->role->select('id')
            ->where('name', 'matches', $search)
            ->where('id', '!in', $selected)
            ->chain([$this, 'applyDependencies'])
            ->toList('id');
    }

    protected function _getResultDisplayName($result) {
        return $result['name'].' ('.$result['priority'].')';
    }

    protected function _renderCollectionList($result) {
        return $this->import->component('RoleList', '~admin/users/roles/', [
                'actions' => false
            ])
            ->setCollection($result);
    }
}
