<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\account\_nodes;

use df\arch;

use df\apex\directory\front\account\_formDelegates\RegisterLocal;

use DecodeLabs\Disciple;
use DecodeLabs\R7\Legacy;

class HttpRegister extends arch\node\Form
{
    public const DEFAULT_ACCESS = arch\IAccess::GUEST;
    public const DEFAULT_EVENT = 'register';

    protected $_invite;

    protected function init(): void
    {
        if (Disciple::isLoggedIn()) {
            throw $this->forceResponse(
                Legacy::$http->defaultRedirect('account/')
            );
        }

        if (isset($this->request['invite'])) {
            $this->_invite = $this->data->fetchForAction(
                'axis://user/Invite',
                ['key' => $this->request['invite']]
            );

            if (!$this->_invite['isActive']) {
                $this->comms->flashError(
                    'invite.inactive',
                    $this->_('The invite link you have followed is no longer active')
                );

                throw $this->forceResponse(
                    Legacy::$http->defaultRedirect('/')
                );
            }
        } else {
            if (!$this->data->user->config->isRegistrationEnabled()) {
                $this->comms->flashError(
                    'registration.disabled',
                    $this->_('Registration for this site is currently disabled')
                );

                throw $this->forceResponse(
                    Legacy::$http->defaultRedirect('/')
                );
            }
        }
    }

    public function getInvite()
    {
        return $this->_invite;
    }

    protected function getInstanceId(): ?string
    {
        if ($this->_invite) {
            return $this->_invite['key'];
        }

        return null;
    }

    protected function loadDelegates(): void
    {
        // Local
        $this->loadDelegate('Local', '~front/account/RegisterLocal')
            ->as(RegisterLocal::class)
            ->setInvite($this->_invite);
    }

    protected function createUi(): void
    {
        $this->view
            ->setTitle('Register for a new account')
            ->setCanonical('account/register');

        $this['Local']
            ->as(RegisterLocal::class)
            ->renderUi();
    }

    protected function onRegisterEvent(...$args)
    {
        return $this['Local']->handleEvent('register', $args);
    }
}
