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
        $model = $this->data->getModel('user');

        return $model->group->fetch()
            ->where('id', 'in', $ids);
    }

    protected function _getResultDisplayName($result) {
        return $result['name'];
    }

    protected function _getSearchResultIdList($search, array $selected) {
        $model = $this->data->getModel('user');

        return $model->group->select('id')
            ->beginWhereClause()
                ->where('name', 'contains', $search)
                ->orWhere('name', 'like', $search)
                ->endClause()
            ->where('id', '!in', $selected)
            ->toList('id');
    }
}
