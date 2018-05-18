<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\aura\theme\facet;

use df;
use df\core;
use df\aura;
use df\arch;
use df\spur;

class CookieNotice extends Base
{
    protected $_privacyRequest;
    protected $_privacyVersion = 0;

    public function __construct(array $config)
    {
        parent::__construct($config);

        $this->_privacyRequest = $config['privacyRequest'] ?? null;
        $this->_privacyVersion = (int)($config['privacyVersion'] ?? 0);
    }

    public function getPrivacyVersion(): int
    {
        return $this->_privacyVersion;
    }

    public function onHtmlViewLayoutRender(aura\view\IHtmlView $view, $content)
    {
        if ($view->context->getRunMode() != 'Http'
        || !$view->shouldRenderBase()) {
            return;
        }

        $view->dfKit->load('df-kit/modal');
        $data = $view->consent->getUserData();

        if ($data['necessary']) {
            return;
        }

        return $view->context->apex->template('~front/cookies/#/elements/Notice.html', [
                'privacyRequest' => $this->_privacyRequest
            ])
            ->renderTo($view)."\n".$content;
    }
}
