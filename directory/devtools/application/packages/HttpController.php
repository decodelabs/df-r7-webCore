<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\application\packages;

use df;
use df\core;
use df\apex;
use df\arch;
use df\spur;
    
class HttpController extends arch\Controller {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function indexHtmlAction() {
        if(!$this->application->isDevelopment()) {
            $this->arch->notify(
                'package.dev',
                $this->_('The package manager can only be used from a development mode installation'),
                'error'
            );

            return $this->http->defaultRedirect();
        }

        $view = $this->aura->getView('Index.html');
        $view['packageList'] = $this->data->getModel('package')->getInstalledPackageList();

        return $view;
    }

    public function refreshAction() {
        $model = $this->data->getModel('package');
        $name = $this->request->query['package'];
        
        if(!$model->updateRemote($name)) {
            $this->arch->notify(
                'package.update',
                $this->_('Package "%n%" could not be updated', ['%n%' => $name]),
                'error'
            );
        } else {
            $this->arch->notify(
                'package.update',
                $this->_('Package "%n%" has been successfully refreshed', ['%n%' => $name]),
                'success'
            );
        }

        return $this->http->defaultRedirect();
    }

    public function refreshAllAction() {
        $model = $this->data->getModel('package');
        $model->updateRemotes();

        $this->arch->notify(
            'package.update',
            $this->_('All package repositories have been refreshed'),
            'success'
        );

        return $this->http->defaultRedirect();
    }
}