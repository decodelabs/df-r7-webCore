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

    protected $_deactivation;

    protected function _init() {
        $this->_deactivation = $this->data->newRecord('axis://user/ClientDeactivation');
    }

    protected function _createUi() {
        $this->content->push(
            $this->import->component('~front/account/Deactivate', $this)
        );
    }

    protected function _onDeactivateEvent() {
        $this->data->newValidator()
            ->addField('reason', 'text')
                ->setMaxLength(255)
                ->end()
            ->addField('reasonOther', 'text')
                ->setMaxLength(255)
                ->setRecordName('reason')
                ->end()
            ->addField('comments', 'text')
                ->end()

            ->validate($this->values)
            ->applyTo($this->_deactivation);

        if($this->isValid()) {
            $client = $this->data->user->client->fetchActive();
            $client->setAsDeactivated();
            $client->save();

            $this->_deactivation->user = $client;
            $this->_deactivation->save();

            $this->comms->componentAdminNotify(
                'users/Deactivation',
                [$this->_deactivation]
            );

            $this->user->logout();
            return $this->http->redirect('account/login');
        }
    }
}