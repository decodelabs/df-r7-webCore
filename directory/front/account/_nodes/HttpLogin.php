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

use DecodeLabs\Disciple;
use DecodeLabs\Exceptional;

class HttpLogin extends arch\node\Form
{
    const CHECK_ACCESS = false;
    const DEFAULT_ACCESS = arch\IAccess::GUEST;
    const DEFAULT_EVENT = 'login';
    const DEFAULT_REDIRECT = '/';

    protected $_adapter;
    protected $_config;

    protected function init()
    {
        if (Disciple::isLoggedIn()) {
            $this->setComplete();
            return $this->_getCompleteRedirect(null, false);
        }

        $this->_config = user\authentication\Config::getInstance();

        if (isset($this->request['adapter'])) {
            $this->_adapter = $this->request['adapter'];

            if (!$this->_config->isAdapterEnabled($this->_adapter)) {
                $this->_adapter = null;
            }
        }

        if (!$this->_adapter) {
            $this->_adapter = $this->_config->getFirstEnabledAdapter();
        }

        if (!$this->_adapter) {
            throw Exceptional::{'df/user/authentication/Setup'}([
                'message' => 'There are no enabled authentication adapters',
            ]);
        }

        if (isset($this->request['rf'])) {
            $redir = $this->request->getRedirectFrom();

            if ($redir->matches('account/')
            && in_array($redir->getNode(), [
                'accessPass', 'logout', 'lostPassword', 'register', 'resetPassword'
            ])) {
                unset($this->request['rf']);
                //return $this->http->redirect($this->request);
            }
        }
    }

    protected function getInstanceId()
    {
        return null;
    }

    public function getAdapter()
    {
        return $this->_adapter;
    }

    public function getConfig()
    {
        return $this->_config;
    }

    protected function loadDelegates()
    {
        $this->loadDelegate($this->_adapter, '~front/account/Login'.$this->_adapter);
    }

    protected function createUi()
    {
        $this->view
            ->setTitle('Sign in to your account')
            ->setCanonical('account/login');

        $enabled = $this->_config->getEnabledAdapters();

        if (count($enabled) > 1) {
            $this->_renderSwitcher($enabled);
        }

        $this[$this->_adapter]->renderUi();
    }

    protected function _renderSwitcher(array $enabled)
    {
        $menu = $this->content->addMenuBar();

        foreach ($enabled as $adapterName => $options) {
            $class = 'df\\user\\authentication\\adapter\\'.$adapterName;

            if (!class_exists($class)) {
                continue;
            }

            $name = $class::getDisplayName();

            $menu->addLink(
                $this->html->link(
                        $this->view->uri->query(['adapter' => $adapterName]),
                        $name
                    )
                    ->isActive($adapterName == $this->_adapter)
            );
        }
    }

    protected function onLoginEvent(...$args)
    {
        $delegate = $this[$this->_adapter];
        $output = $delegate->handleEvent('login', $args);

        if ($delegate->isComplete()) {
            $this->setComplete();
        }

        return $output;
    }
}
