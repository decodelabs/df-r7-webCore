<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\fire\interact;

use df;
use df\core;
use df\fire;
use df\flow;
use df\apex;
use df\mesh;
    
interface ICommentAwareEntity extends mesh\entity\IEntity {
    public function getCommentNotification(apex\models\content\comment\Record $comment);
}