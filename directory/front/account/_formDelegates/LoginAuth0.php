<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\account\_formDelegates;

use DecodeLabs\R7\Legacy;

use df\arch;

class LoginAuth0 extends arch\node\form\Delegate implements arch\node\IParentUiHandlerDelegate
{
    use arch\node\TForm_ParentUiHandlerDelegate;

    public const DEFAULT_REDIRECT = '/';

    protected function createUi(): void
    {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Sign-in'));

        // Button
        $fs->addButtonArea(
            $this->html->eventButton(
                $this->eventName('login'),
                $this->_('Sign in')
            )
                ->setIcon('user'),
            $this->html->cancelEventButton()
        );
    }

    protected function onLoginEvent()
    {
        return Legacy::$http->redirect($this->uri->directoryRequest(
            'account/auth0/login',
            $this->request->getRedirectFrom(),
            $this->request->getRedirectTo()
        ));
    }
}
