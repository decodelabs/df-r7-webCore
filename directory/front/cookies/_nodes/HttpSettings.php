<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\cookies\_nodes;

use DecodeLabs\Genesis;

use DecodeLabs\R7\Legacy;
use df\arch;

class HttpSettings extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function executeAsHtml()
    {
        return $this->apex->view('cookies/Settings.html', function ($view) {
            $view->setAjaxData([
                'modalClass' => 'cookie-settings'
            ]);

            $view->setTitle('Cookie Settings')
                ->setMeta('description', 'Select which types of cookies you would like to enable on ' . Genesis::$hub->getApplicationName())
                ->setCanonical('cookies/settings');

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


            $request = clone $this->request;
            $isGlobal = false;

            try {
                if ($referrer = Legacy::$http->getReferrer()) {
                    $referrerDomain = $this->uri($referrer)->getDomain();
                    $referrer = Legacy::$http->localReferrerToRequest($referrer);

                    if ($referrer && !$referrer->matches($request)) {
                        if ($referrerDomain !== Legacy::$http->getHost()) {
                            $request->setRedirectTo($referrer);
                            $isGlobal = true;
                        }
                    }
                }
            } catch (\Throwable $e) {
            }

            yield 'formRequest' => $request;
            yield 'isGlobal' => $isGlobal;
        });
    }

    public function executePost()
    {
        $validator = $this->data->newValidator()
            ->addField('id', 'guid')
            ->addRequiredField('preferences', 'boolean')
            ->addRequiredField('statistics', 'boolean')
            ->addRequiredField('marketing', 'boolean')
            ->validate(Legacy::$http->getPostData());


        $this->consent->setUserData([
            'id' => $validator['id'],
            'version' => 0,
            'preferences' => $validator['preferences'],
            'statistics' => $validator['statistics'],
            'marketing' => $validator['marketing']
        ]);

        if (Legacy::$http->isAjaxRequest()) {
            return Legacy::$http->ajaxResponse('', [
                'isComplete' => true,
                'reload' => true
            ]);
        } else {
            return Legacy::$http->defaultRedirect('/', true);
        }
    }
}
