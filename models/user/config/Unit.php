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

    const USE_TREE = true;
    const ID = 'Users';

    public function getDefaultValues() {
        return [
            'registrationEnabled' => true,
            'verifyEmail' => false,
            'loginOnRegistration' => true,
            'registrationLandingPage' => '/account/'
        ];
    }

    public function isRegistrationEnabled($flag=null) {
        if($flag !== null) {
            $this->values->registrationEnabled = (bool)$flag;
            return $this;
        }

        return (bool)$this->values['registrationEnabled'];
    }

    public function shouldVerifyEmail($flag=null) {
        if($flag !== null) {
            $this->values->verifyEmail = (bool)$flag;
            return $this;
        }

        return (bool)$this->values['verifyEmail'];
    }

    public function shouldLoginOnRegistration($flag=null) {
        if($flag !== null) {
            $this->values->loginOnRegistration = (bool)$flag;
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
}