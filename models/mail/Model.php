<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\mail;

use df;
use df\core;
use df\apex;
use df\axis;
    
class Model extends axis\Model implements core\mail\IMailModel {

    public function storeDevMail(core\mail\IMessage $message) {
    	return $this->devMail->store($message);
    }
}