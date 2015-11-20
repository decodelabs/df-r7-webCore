<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\account\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\user;

class HttpRegister extends arch\node\Form {

    const DEFAULT_ACCESS = arch\IAccess::GUEST;
    const DEFAULT_EVENT = 'register';

    protected $_invite;

    protected function init() {
        if($this->user->isLoggedIn()) {
            return $this->http->defaultRedirect('account/');
        }

        if(isset($this->request['invite'])) {
            $this->_invite = $this->data->fetchForAction(
                'axis://user/Invite',
                ['key' => $this->request['invite']]
            );

            if(!$this->_invite['isActive']) {
                $this->comms->flashError(
                    'invite.inactive',
                    $this->_('The invite link you have followed is no longer active')
                );

                return $this->http->defaultRedirect('/');
            }
        } else {
            if(!$this->data->user->config->isRegistrationEnabled()) {
                $this->comms->flashError(
                    'registration.disabled',
                    $this->_('Registration for this site is currently disabled')
                );

                return $this->http->defaultRedirect('/');
            }
        }
    }

    public function getInvite() {
        return $this->_invite;
    }

    protected function getInstanceId() {
        if($this->_invite) {
            return $this->_invite['key'];
        }

        return parent::getInstanceId();
    }

    protected function loadDelegates() {
        $this->loadDelegate('Local', '~front/account/RegisterLocal')
            ->setInvite($this->_invite);
    }

    protected function createUi() {
        $this['Local']->renderUi();
    }

    protected function onRegisterEvent() {
        return $this['Local']->handleEvent('register', func_get_args());
    }
}