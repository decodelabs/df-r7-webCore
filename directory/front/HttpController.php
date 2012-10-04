<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front;

use df;
use df\core;
use df\arch;

class HttpController extends arch\Controller {
    
    const CHECK_ACCESS = false;
    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function indexHtmlAction() {
        return $this->aura->getView('Index.html');
    }

    public function crossdomainXmlAction() {
        return $this->aura->getView('Crossdomain.xml');
    }

    public function robotsTxtAction() {
        return $this->aura->getView('Robots.txt');
    }
}
