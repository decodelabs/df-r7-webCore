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

        $this->data->checkAccess($model->client);

    	$view['clientList'] = $model->client->select()

    		->correlate('COUNT(groupBridge.group) as groups')
    			->from($model->groupBridge, 'groupBridge')
    			->on('groupBridge.client', '=', 'client.@primary')
    			->endCorrelation()

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
        $view = $this->aura->getView('Details.html');

        $view['client'] = $this->data->fetchForAction(
            'axis://user/Client',
            $this->request->query['client']
        );

    	return $view;
    }
}