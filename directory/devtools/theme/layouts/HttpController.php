<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\theme\layouts;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;
    
class HttpController extends arch\Controller {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function indexHtmlAction() {
    	$config = aura\view\layout\Config::getInstance($this->application);
    	$view = $this->aura->getView('Index.html');

    	$view['layoutList'] = $config->getAllLayoutDefinitions();

    	return $view;
    }

    public function detailsHtmlAction() {
    	$config = aura\view\layout\Config::getInstance($this->application);
    	$view = $this->aura->getView('Details.html');

    	if(!$view['layout'] = $config->getLayoutDefinition($this->request->query['layout'])) {
    		$this->throwError(404, 'Layout not found');
    	}

    	return $view;
    }
}