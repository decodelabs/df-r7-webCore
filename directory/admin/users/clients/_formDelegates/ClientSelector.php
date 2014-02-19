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
use df\opal;

class ClientSelector extends arch\form\template\SearchSelectorDelegate {

    protected function _fetchResultList(array $ids) {
        return $this->data->user->client->fetch()
            ->countRelation('groups')
            ->where('id', 'in', $ids)
            ->chain([$this, 'applyDependencies'])
            ->orderBy('fullName ASC');
    }

    protected function _getSearchResultIdList($search, array $selected) {
        return $this->data->user->client->select('id')
            ->beginWhereClause()
                ->where('fullName', 'matches', $search)
                ->orWhere('nickName', 'matches', $search)
                ->endClause()
            ->where('id', '!in', $selected)
            ->chain([$this, 'applyDependencies'])
            ->toList('id');
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