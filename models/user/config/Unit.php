<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\user\config;

use df;
use df\core;
use df\apex;
use df\axis;
use df\user;

class Unit extends axis\unit\config\Base {

    const ID = 'Users';

    public function getDefaultValues() {
        return [
            'registrationEnabled' => false,
            'verifyEmail' => false,
            'loginOnRegistration' => true,
            'registrationLandingPage' => '/',
            'checkPasswordStrength' => true,
            'minPasswordStrength' => 18,
            'inviteCap' => null,
            'inviteGroupCap' => []
        ];
    }

    public function isRegistrationEnabled(bool $flag=null) {
        if($flag !== null) {
            $this->values->registrationEnabled = $flag;
            return $this;
        }

        return (bool)$this->values['registrationEnabled'];
    }

    public function shouldVerifyEmail(bool $flag=null) {
        if($flag !== null) {
            $this->values->verifyEmail = $flag;
            return $this;
        }

        return (bool)$this->values['verifyEmail'];
    }

    public function shouldLoginOnRegistration(bool $flag=null) {
        if($flag !== null) {
            $this->values->loginOnRegistration = $flag;
            return $this;
        }

        return (bool)$this->values['loginOnRegistration'];
    }

    public function setRegistrationLandingPage($request) {
        $this->values->registrationLandingPage = (string)$request;
        return $this;
    }

    public function getRegistrationLandingPage() {
        return $this->values->get('registrationLandingPage', '/account/');
    }


    public function shouldCheckPasswordStrength(bool $flag=null) {
        if($flag !== null) {
            $this->values->checkPasswordStrength = $flag;
            return $this;
        }

        return (bool)$this->values->get('checkPasswordStrength', true);
    }

    public function setMinPasswordStrength($min) {
        $this->values->minPasswordStrength = (int)$min;
        return $this;
    }

    public function getMinPasswordStrength() {
        return (int)$this->values->get('minPasswordStrength', 18);
    }


    public function setInviteCap($cap) {
        if(is_numeric($cap) && $cap > 0) {
            $cap = (int)$cap;
        } else {
            $cap = null;
        }

        $this->values->inviteCap = $cap;
        return $this;
    }

    public function getInviteCap() {
        $output = $this->values['inviteCap'];

        if(is_numeric($output) && $output > 0) {
            return (int)$output;
        } else {
            return null;
        }
    }

    public function setInviteGroupCap($groupId, $cap) {
        if(is_numeric($cap) && $cap > 0) {
            $cap = (int)$cap;
        } else {
            $cap = null;
        }

        $this->values->inviteGroupCap[$groupId] = $cap;
        return $this;
    }

    public function getInviteGroupCap($groupId) {
        return $this->values->inviteGroupCap[$groupId];
    }

    public function getInviteGroupCaps() {
        return $this->values->inviteGroupCap->toArray();
    }

    public function hasInviteCap() {
        return $this->values['inviteCap'] !== null
            || !$this->values->inviteGroupCap->isEmpty();
    }
}