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
        $query = $this->data->user->group->fetch()
            ->countRelation('users')
            ->countRelation('roles')
            ->where('id', 'in', $ids);

        return $query;
    }

    protected function _getSearchResultIdList($search, array $selected) {
        $query = $this->data->user->group->select('id')
            ->beginWhereClause()
                ->where('name', 'contains', $search)
                ->orWhere('name', 'like', $search)
                ->endClause()
            ->where('id', '!in', $selected);


        return $query->toList('id');
    }

    protected function _renderCollectionList($result) {
        return $this->import->component('GroupList', '~admin/users/groups/', [
                'actions' => false
            ])
            ->setCollection($result);
    }
}
