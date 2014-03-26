<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\models;

use df;
use df\core;
use df\apex;
use df\arch;
use df\axis;
use df\opal;

class HttpController extends arch\Controller {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function fetchUnit($view, $type=null) {
        $probe = new axis\introspector\Probe($this->application);
        $view['unit'] = $probe->inspectUnit($this->request->query['unit']);

        if($type !== null) {
            if($view['unit']->getType() != $type) {
                $this->throwError(403, 'Unit is not a '.$type);
            }
        }
    }
}