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
    
class HttpEdit extends HttpAdd {

    protected function init() {
        $this->_comment = $this->data->fetchForAction(
            'axis://interact/Comment',
            $this->request->query['comment'],
            'edit'
        );
    }

    protected function getInstanceId() {
        return $this->_comment['id'];
    }

    protected function setDefaultValues() {
        $this->values->importFrom($this->_comment, [
            'isLive', 'body'
        ]);
    }

    protected function _renderHistory($fs) {
        if($replyTo = $this->_comment['inReplyTo']) {
            $fs->addFieldArea($this->_('Reply to'))
                ->addTemplate('~/comments/#/elements/List.html', [
                        'commentList' => [$replyTo],
                        'showFooter' => false
                    ]);
        }
    }

    protected function _prepareRecord() {
        // Don't need to do anything, but need to override add version
    }
}