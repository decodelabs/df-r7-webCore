<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\content\comment;

use df\core;
use df\axis;
use df\opal;
use df\user;

class Record extends opal\record\Base {

    public function getUniqueId() {
        return md5($this['id'].':'.$this['date']->toTimestamp());
    }

    public function getPopulatedTreeReplies($fetchTree=false) {
        if($this->replies->count()) {
            return $this->replies->toArray();
        }

        $output = [];

        if(!$this->getRaw('root')) {
            return $output;
        }

        if(!$this->replyTree->count()) {
            if(!$fetchTree) {
                return $output;
            }

            $this->replyTree->populateList(
                $tree = $this->replyTree->fetch()->toArray()
            );
        } else {
            $tree = $this->replyTree->toArray();
        }

        $treeIndex = [(string)$this['id'] => $this];

        foreach($tree as $node) {
            $treeIndex[(string)$node['id']] = $node;
        }

        foreach($treeIndex as $node) {
            $id = (string)$node['#inReplyTo'];

            if(isset($treeIndex[$id])) {
                $treeIndex[$id]->replies->populate($node);
            }
        }

        return $this->replies->toArray();
    }
}
