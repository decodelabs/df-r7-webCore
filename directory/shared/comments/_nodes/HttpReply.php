<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\shared\comments\_nodes;

use DecodeLabs\Disciple;

class HttpReply extends HttpAdd
{
    protected $_parentComment;

    protected function init(): void
    {
        $this->_parentComment = $this->data->fetchForAction(
            'axis://content/Comment',
            $this->request['comment']
        );

        $this->_comment = $this->data->newRecord('axis://content/Comment');
    }

    protected function getInstanceId(): ?string
    {
        return $this->_parentComment['id'];
    }

    protected function _renderHistory($fs)
    {
        $fs->addField($this->_('Reply to'))->push(
            $this->apex->template('~/comments/#/elements/List.html', [
                    'commentList' => [$this->_parentComment],
                    'showFooter' => false
                ])
                ->setRenderTarget($this->view)
        );
    }

    protected function _prepareRecord()
    {
        $this->_comment->topic = $this->_parentComment['topic'];
        $this->_comment->owner = Disciple::getId();

        if (!$root = $this->_parentComment['root']) {
            $root = $this->_parentComment;
        }

        $this->_comment->root = $root;
        $this->_comment->inReplyTo = $this->_parentComment;
    }
}
