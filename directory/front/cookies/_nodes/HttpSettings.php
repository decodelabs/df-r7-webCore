<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\cookies\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpSettings extends arch\node\Base
{
    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function executeAsHtml()
    {
        return $this->apex->view('cookies/Settings.html', function ($view) {
            $view->setAjaxData([
                'modalClass' => 'cookie-settings'
            ]);

            yield 'cookieData' => $this->consent->getUserData();

            //try {
            $notice = $view->getTheme()->getFacet('cookieNotice');
            yield 'privacyRequest' => $notice->getPrivacyRequest();
            //} catch (\Throwable $e) {
            //}
        });
    }

    public function executePost()
    {
        $validator = $this->data->newValidator()
            ->addRequiredField('preferences', 'boolean')
            ->addRequiredField('statistics', 'boolean')
            ->addRequiredField('marketing', 'boolean')
            ->validate($this->http->getPostData());

        $this->consent->setUserData([
            'version' => 0,
            'preferences' => $validator['preferences'],
            'statistics' => $validator['statistics'],
            'marketing' => $validator['marketing']
        ]);

        if ($this->http->isAjaxRequest()) {
            return $this->http->ajaxResponse('done', [
                'isComplete' => true,
                'reload' => true
            ]);
        } else {
            return $this->http->redirect('/');
        }
    }
}
