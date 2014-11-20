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
    
class HttpLogin extends arch\form\Action {

    const CHECK_ACCESS = false;
    const DEFAULT_ACCESS = arch\IAccess::GUEST;
    const DEFAULT_EVENT = 'login';
    const DEFAULT_REDIRECT = '/';

    protected $_adapter;
    protected $_config;

    protected function _init() {
        if($this->user->client->isLoggedIn()) {
            $this->complete();
            return $this->http->defaultRedirect('account/');
        }

        $this->_config = user\authentication\Config::getInstance();

        if(isset($this->request->query->adapter)) {
            $this->_adapter = $this->request->query['adapter'];

            if(!$this->_config->isAdapterEnabled($this->_adapter)) {
                $this->_adapter = null;
            }
        }

        if(!$this->_adapter) {
            $this->_adapter = $this->_config->getFirstEnabledAdapter();
        }

        if(!$this->_adapter) {
            $this->throwError(500, 'There are no enabled authentication adapters');
        }
    }

    protected function _setupDelegates() {
        $this->loadDelegate($this->_adapter, '~front/account/Login'.$this->_adapter);
    }

    protected function _createUi() {
        $enabled = $this->_config->getEnabledAdapters();

        if(count($enabled) > 1) {
            $this->content->push(
                $this->import->component(
                    '~front/account/LoginAdapterSwitcher', 
                    $this,
                    $enabled,
                    $this->_adapter
                )
            );
        }

        $this->getDelegate($this->_adapter)->renderUi();
    }

    protected function _onLoginEvent() {
        $delegate = $this->getDelegate($this->_adapter);
        $output = $delegate->handleEvent('login', func_get_args());
        
        if($delegate->isComplete()) {
            $this->complete();
        }

        return $output;
    }
}