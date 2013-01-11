<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\_formDelegates;

use df;
use df\core;
use df\apex;
use df\arch;
    
class ClientSelector extends arch\form\template\SearchSelectorDelegate {

    protected function _fetchResultList(array $ids) {
        $model = $this->data->getModel('user');

        return $model->client->fetch()
            ->where('id', 'in', $ids);
    }

    protected function _getResultDisplayName($result) {
        return $result['fullName'];
    }

    protected function _getSearchResultIdList($search, array $selected) {
        $model = $this->data->getModel('user');

        return $model->client->select('id')
            ->beginWhereClause()
                ->where('fullName', 'contains', $search)
                ->orWhere('nickName', 'contains', $search)
                ->orWhere('fullName', 'like', $search)
                ->orWhere('nickName', 'like', $search)
                ->endClause()
            ->where('id', '!in', $selected)
            ->toList('id');
    }
}