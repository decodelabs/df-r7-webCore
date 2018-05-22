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

            yield 'cookieData' => $data = $this->consent->getUserData(
                $this->request['id']
            );

            yield 'preferences' => true;
            yield 'statistics' => true;
            yield 'marketing' => true;

            try {
                $notice = $view->getTheme()->getFacet('cookieNotice');
                yield 'privacyRequest' => $notice->getPrivacyRequest();

                yield 'preferences' => $notice->isCategoryEnabled('preferences');
                yield 'statistics' => $notice->isCategoryEnabled('statistics');
                yield 'marketing' => $notice->isCategoryEnabled('marketing');
            } catch (\Throwable $e) {
            }
        });
    }

    public function executePost()
    {
        $validator = $this->data->newValidator()
            ->addField('id', 'guid')
            ->addRequiredField('preferences', 'boolean')
            ->addRequiredField('statistics', 'boolean')
            ->addRequiredField('marketing', 'boolean')
            ->validate($this->http->getPostData());


        $this->consent->setUserData([
            'id' => $validator['id'],
            'version' => 0,
            'preferences' => $validator['preferences'],
            'statistics' => $validator['statistics'],
            'marketing' => $validator['marketing']
        ]);

        if ($this->http->isAjaxRequest()) {
            return $this->http->ajaxResponse('', [
                'isComplete' => true,
                'reload' => true
            ]);
        } else {
            return $this->http->defaultRedirect('/', true);
        }
    }
}
