<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\aura\theme\facet;

use DecodeLabs\Genesis;

use DecodeLabs\R7\Legacy;
use df\aura;

class CookieNotice extends Base
{
    protected $_privacyRequest;
    protected $_privacyVersion = 0;
    protected $_categories = [];
    protected bool $_customRenderer = false;

    public function __construct(array $config)
    {
        parent::__construct($config);

        $this->_privacyRequest = $config['privacyRequest'] ?? null;
        $this->_privacyVersion = (int)($config['privacyVersion'] ?? 0);
        $this->_categories = (array)($config['categories'] ?? []);
        $this->_customRenderer = (bool)($config['customRenderer'] ?? false);
    }

    public function getPrivacyRequest(): ?string
    {
        return $this->_privacyRequest;
    }

    public function getPrivacyVersion(): int
    {
        return $this->_privacyVersion;
    }

    public function isCategoryEnabled(string $category): bool
    {
        if (isset($this->_categories[$category])) {
            return (bool)$this->_categories[$category];
        } else {
            return true;
        }
    }

    public function onHtmlViewLayoutRender(aura\view\IHtmlView $view, $content)
    {
        if (
            Genesis::$kernel->getMode() != 'Http' ||
            !$view->shouldRenderBase()
        ) {
            return;
        }

        $data = $view->consent->getUserData();

        if ($data['necessary']) {
            return;
        }

        $agent = Legacy::$http->getUserAgent() ?? '';

        if ($view->data->user->agent->isBot($agent)) {
            return;
        }

        if ($this->_customRenderer) {
            return;
        }

        $view->dfKit->load('df-kit/modal');

        return $view->context->apex->template('~front/cookies/#/elements/Notice.html', [
                'privacyRequest' => $this->_privacyRequest
            ])
            ->renderTo($view) . "\n" . $content;
    }
}
