<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class UserLink extends arch\Component {

    protected $_user;
    protected $_icon = 'user';
    protected $_disposition = 'informative';
    protected $_isNullable = false;

    protected function _init($user=null) {
        if($user) {
            $this->setUser($user);
        }
    }

// User
    public function setUser($user) {
        $this->_user = $user;
        return $this;
    }

    public function getUser() {
        return $this->_user;
    }

// Icon
    public function setIcon($icon) {
        $this->_icon = $icon;
        return $this;
    }

    public function getIcon() {
        return $this->_icon;
    }

// Disposition
    public function setDisposition($disposition) {
        $this->_disposition = $disposition;
        return $this;
    }

    public function getDisposition() {
        return $this->_disposition;
    }

// Nullable
    public function isNullable($flag=null) {
        if($flag !== null) {
            $this->_isNullable = (bool)$flag;
            return $this;
        }

        return $this->_isNullable;
    }

// Render
    protected function _execute() {
        if($this->_user === null && $this->_isNullable) {
            return null;
        }

        if(!$this->_user) {
            return $this->getView()->html->link('#', 'not found')
                ->isDisabled(true)
                ->setIcon('error')
                ->addClass('state-error');
        }

        $name = $this->_user['fullName'];

        return $this->getView()->html->link(
                '~admin/users/details?user='.$this->_user['id'],
                $name
            )
            ->setIcon($this->_icon)
            ->setDisposition($this->_disposition);
    }
}