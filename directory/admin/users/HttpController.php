<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users;

use df;
use df\core;
use df\apex;
use df\arch;
use df\user;
    
class HttpController extends arch\Controller {

    public function indexHtmlAction() {
    	$model = $this->data->getModel('user');
    	$view = $this->aura->getView('Index.html');

    	$view['clientList'] = $model->client->select()

    		->leftJoin('COUNT(group_id) as groups')
    			->from($model->groupBridge, 'groupBridge')
    			->on('groupBridge.client_groups_id', '=', 'client.id')
    			->endJoin()

			->groupBy('client.id')

			->paginate()
				->setOrderableFields(
					'email', 'fullName', 'nickName', 'status', 'joinDate',
					'loginDate', 'timezone', 'country', 'language'
				)
				->setDefaultOrder('fullName')
				->setDefaultLimit(30)
				->applyWith($this->request->query);

		return $view;
    }

    public function detailsHtmlAction() {
    	$model = $this->data->getModel('user');
    	$view = $this->aura->getView('Details.html');

    	if(!$view['client'] = $model->client->fetchByPrimary($this->request->query['client'])) {
    		$this->throwError(404, 'User not found');
    	}

    	return $view;
    }
}