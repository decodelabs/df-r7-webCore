<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\user\config;

use df\axis;

class Unit extends axis\unit\Config
{
    public const ID = 'Users';

    public function getDefaultValues(): array
    {
        return [
            'registrationEnabled' => false,
            'verifyEmail' => false,
            'loginOnRegistration' => true,
            'registrationLandingPage' => '/',
            'checkPasswordStrength' => true,
            'minPasswordStrength' => 18
        ];
    }

    public function isRegistrationEnabled(bool $flag = null)
    {
        if ($flag !== null) {
            $this->values->registrationEnabled = $flag;
            return $this;
        }

        return (bool)$this->values['registrationEnabled'];
    }

    public function shouldVerifyEmail(bool $flag = null)
    {
        if ($flag !== null) {
            $this->values->verifyEmail = $flag;
            return $this;
        }

        return (bool)$this->values['verifyEmail'];
    }

    public function shouldLoginOnRegistration(bool $flag = null)
    {
        if ($flag !== null) {
            $this->values->loginOnRegistration = $flag;
            return $this;
        }

        return (bool)$this->values['loginOnRegistration'];
    }

    public function setRegistrationLandingPage($request)
    {
        $this->values->registrationLandingPage = (string)$request;
        return $this;
    }

    public function getRegistrationLandingPage()
    {
        return $this->values->get('registrationLandingPage', '/account/');
    }


    public function shouldCheckPasswordStrength(bool $flag = null)
    {
        if ($flag !== null) {
            $this->values->checkPasswordStrength = $flag;
            return $this;
        }

        return (bool)$this->values->get('checkPasswordStrength', true);
    }

    public function setMinPasswordStrength($min)
    {
        $this->values->minPasswordStrength = (int)$min;
        return $this;
    }

    public function getMinPasswordStrength()
    {
        return (int)$this->values->get('minPasswordStrength', 18);
    }
}
