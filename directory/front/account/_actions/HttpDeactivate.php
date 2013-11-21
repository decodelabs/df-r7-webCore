<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\account\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpDeactivate extends arch\form\Action {

    const DEFAULT_ACCESS = arch\IAccess::CONFIRMED;
    const DEFAULT_EVENT = 'deactivate';

    protected function _createUi() {
        $this->content->push(
            $this->import->component(
                'Deactivate', 
                '~front/account/', 
                $this
            )
        );
    }

    protected function _onDeactivateEvent() {
        if($this->isValid()) {
            $client = $this->data->user->client->fetchActive();
            $client->setAsDeactivated();
            $client->save();

            $this->user->logout();
            return $this->http->redirect('account/login');
        }
    }
}