<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\mail\devMail;

use df;
use df\core;
use df\axis;
use df\opal;

class Record extends opal\query\record\Base implements core\mail\IDevMailRecord {
    
    public function getId() {
        return $this['id'];
    }
    
    public function getFromAddress() {
        return core\mail\Address::factory($this['from']);
    }

    public function getToAddresses() {
        $output = array();

        foreach(explode(',', $this['to']) as $address) {
            $output[] = core\mail\Address::factory($address);
        }

        return $output;
    }

    public function getSubject() {
        return $this['subject'];
    }

    public function getBodyString() {
        return $this['body'];
    }

    public function getDate() {
        return $this['date'];
    }

    public function isPrivate() {
        return $this['isPrivate'];
    }

    public function toMessage() {
        return core\mail\Message::fromString($this['body']);
    }
}