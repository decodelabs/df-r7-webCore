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
use df\user;
    
class HttpRegister extends arch\form\Action {

    const DEFAULT_ACCESS = arch\IAccess::GUEST;
    const DEFAULT_EVENT = 'register';

    protected $_invite;

    protected function _init() {
        if($this->user->isLoggedIn()) {
            return $this->http->defaultRedirect('account/');
        }

        if(isset($this->request->query->invite)) {
            $this->_invite = $this->data->fetchForAction(
                'axis://user/Invite',
                ['key' => $this->request->query['invite']]
            );

            if(!$this->_invite['isActive']) {
                $this->comms->flash(
                    'invite.inactive',
                    $this->_(
                        'The invite link you have followed is no longer active'
                    ),
                    'error'
                );

                return $this->http->defaultRedirect('/');
            }
        } else {
            if(!$this->data->user->config->isRegistrationEnabled()) {
                $this->comms->flash(
                    'registration.disabled',
                    $this->_(
                        'Registration for this site is currently disabled'
                    ),
                    'error'
                );

                return $this->http->defaultRedirect('/');
            }
        }
    }

    public function getInvite() {
        return $this->_invite;
    }

    protected function _getDataId() {
        if($this->_invite) {
            return $this->_invite['key'];
        }

        return parent::_getDataId();
    }

    protected function _setupDelegates() {
        $this->loadDelegate('Local', '~front/account/RegisterLocal')
            ->setInvite($this->_invite);
    }

    protected function _createUi() {
        $this->getDelegate('Local')->renderUi();
    }

    protected function _onRegisterEvent() {
        return $this->getDelegate('Local')->handleEvent('register', func_get_args());
    }
}