<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\clients\_formDelegates;

use df;
use df\core;
use df\apex;
use df\arch;
    
class ClientSelector extends arch\form\template\SearchSelectorDelegate {

    protected function _fetchResultList(array $ids) {
        $query = $this->data->user->client->fetch()
            ->countRelation('groups')
            ->where('id', 'in', $ids)
            ->orderBy('fullName ASC');

        return $query;
    }

    protected function _getSearchResultIdList($search, array $selected) {
        $query = $this->data->user->client->select('id')
            ->beginWhereClause()
                ->where('fullName', 'contains', $search)
                ->orWhere('nickName', 'contains', $search)
                ->orWhere('fullName', 'like', $search)
                ->orWhere('nickName', 'like', $search)
                ->endClause()
            ->where('id', '!in', $selected);

        return $query->toList('id');
    }

    protected function _getResultDisplayName($result) {
        return $result['fullName'];
    }

    protected function _renderCollectionList($result) {
        return $this->import->component('UserList', '~admin/users/clients/', [
                'actions' => false
            ])
            ->setCollection($result);
    }
}