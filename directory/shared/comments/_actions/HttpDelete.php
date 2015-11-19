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

class HttpDelete extends arch\action\DeleteForm {

    const ITEM_NAME = 'comment';

    protected $_comment;

    protected function init() {
        $this->_comment = $this->data->fetchForAction(
            'axis://content/Comment',
            $this->request['comment'],
            'delete'
        );
    }

    protected function getInstanceId() {
        return $this->_comment['id'];
    }

    protected function createItemUi($container) {
        $container->addAttributeList($this->_comment)
            ->addField('owner', function($comment) {
                return $this->apex->component('~admin/users/clients/UserLink', $comment['owner']);
            })
            ->addField('date', $this->_('Posted'), function($comment) {
                return $this->html->timeSince($comment['date']);
            })
            ->addField('body', function($comment) {
                return $this->html->convert($comment['body'], $comment['format']);
            });
    }

    protected function apply() {
        $this->data->content->comment->deleteRecord($this->_comment);
    }
}