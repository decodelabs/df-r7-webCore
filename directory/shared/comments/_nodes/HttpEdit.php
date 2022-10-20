<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\shared\comments\_nodes;

class HttpEdit extends HttpAdd
{
    protected function init()
    {
        $this->_comment = $this->data->fetchForAction(
            'axis://content/Comment',
            $this->request['comment']
        );
    }

    protected function getInstanceId()
    {
        return $this->_comment['id'];
    }

    protected function setDefaultValues(): void
    {
        $this->values->importFrom($this->_comment, [
            'isLive', 'body'
        ]);
    }

    protected function _renderHistory($fs)
    {
        if ($replyTo = $this->_comment['inReplyTo']) {
            $fs->addField($this->_('Reply to'))->push(
                $this->apex->template('~/comments/#/elements/List.html', [
                    'commentList' => [$replyTo],
                    'showFooter' => false
                ])
            );
        }
    }

    protected function _prepareRecord()
    {
        // Don't need to do anything, but need to override add version
    }
}
