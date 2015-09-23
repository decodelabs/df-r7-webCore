<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\shared\comments\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpReply extends HttpAdd {

    protected $_parentComment;

    protected function init() {
        $this->_parentComment = $this->data->fetchForAction(
            'axis://interact/Comment',
            $this->request->query['comment'],
            'reply'
        );

        $this->_comment = $this->data->newRecord('axis://interact/Comment');
    }

    protected function getInstanceId() {
        return $this->_parentComment['id'];
    }

    protected function _renderHistory($fs) {
        $fs->addFieldArea($this->_('Reply to'))
            ->addTemplate('~/comments/#/elements/List.html', [
                    'commentList' => [$this->_parentComment],
                    'showFooter' => false
                ]);
    }

    protected function _prepareRecord() {
        $this->_comment->topic = $this->_parentComment['topic'];
        $this->_comment->owner = $this->user->client->getId();

        if(!$root = $this->_parentComment['root']) {
            $root = $this->_parentComment;
        }

        $this->_comment->root = $root;
        $this->_comment->inReplyTo = $this->_parentComment;
    }
}