<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\shared\comments\_components;

use df;
use df\core;
use df\apex;
use df\arch;
use df\mesh;

class Comment extends arch\component\Base {

    protected $_entityLocator;
    protected $_showInactive = false;
    protected $_displayAsTree = false;
    protected $_showForm = true;

    protected function init($entityLocator=null) {
        if($entityLocator) {
            $this->setEntityLocator($entityLocator);
        }
    }

    public function setEntityLocator($locator) {
        $this->_entityLocator = mesh\entity\Locator::factory($locator);
        return $this;
    }

    public function getEntityLocator() {
        return $this->_entityLocator;
    }

    public function shouldShowInactive(bool $flag=null) {
        if($flag !== null) {
            $this->_showInactive = $flag;
            return $this;
        }

        return $this->_showInactive;
    }

    public function shouldDisplayAsTree(bool $flag=null) {
        if($flag !== null) {
            $this->_displayAsTree = $flag;
            return $this;
        }

        return $this->_displayAsTree;
    }

    public function shouldShowForm(bool $flag=null) {
        if($flag !== null) {
            $this->_showForm = $flag;
            return $this;
        }

        return $this->_showForm;
    }

    protected function _execute() {
        if(!$this->_entityLocator) {
            throw core\Error::{'mesh/ENoEntity,ENotFound'}(
                'Comment entity locator has not been set'
            );
        }

        $template = $this->apex->template('~/comments/#/elements/List.html');
        $template['displayAsTree'] = $this->_displayAsTree;

        $model = $this->data->getModel('content');
        $limit = 30;

        $query = $model->comment->fetch()
            ->populateSelect('owner', 'id', 'fullName')
            ->where('topic', '=', $this->_entityLocator);

        if(!$this->_showInactive) {
            $query->where('isLive', '=', true);
        }

        if($this->_displayAsTree) {
            $limit = 15;
            $query->where('root', '=', null)
                ->populate('replyTree')
                ->populateSelect('replyTree.owner', 'id', 'fullName')
                ;
        } else {
            $query->populate('inReplyTo')
                ->populateSelect('inReplyTo.owner', 'id', 'fullName');
        }

        $query->paginate()
            ->setDefaultLimit($limit)
            ->applyWith($this->request->query);

        $template['commentList'] = $query;
        $template['showForm'] = $this->_showForm;
        $template['entity'] = $this->_entityLocator;

        return $template;
    }
}