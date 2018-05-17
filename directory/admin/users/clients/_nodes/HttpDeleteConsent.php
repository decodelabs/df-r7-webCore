<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\clients\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpDeleteConsent extends arch\node\ConfirmForm
{
    const DISPOSITION = 'negative';

    protected $_user;
    protected $_consent;

    protected function init()
    {
        $this->_user = $this->scaffold->getRecord();

        $this->_consent = $this->data->fetchOrCreateForAction(
            'axis://cookie/Consent',
            ['user' => $this->_user['id']]
        );
    }

    protected function getInstanceId()
    {
        return $this->_user['id'];
    }

    protected function getMainMessage()
    {
        return $this->_('Are you sure you want to delete this user\'s cookie consent?');
    }

    protected function createItemUi($container)
    {
        $container->addAttributeList($this->_consent)
            ->addField('user', function () {
                return $this->_user['fullName'];
            })
            ->addField('creationDate', function ($consent) {
                return $this->html->timeFromNow($consent['creationDate']);
            })
            ->addField('preferenceCookies', function ($consent) {
                return $this->html->timeFromNow($consent['preferences']);
            })
            ->addField('statisticsCookies', function ($consent) {
                return $this->html->timeFromNow($consent['statistics']);
            })
            ->addField('marketingCookies', function ($consent) {
                return $this->html->timeFromNow($consent['marketing']);
            });
    }

    protected function customizeMainButton($button)
    {
        $button->setBody($this->_('Delete'))
            ->setIcon('delete');
    }

    protected function apply()
    {
        $this->_consent->delete();
    }
}
