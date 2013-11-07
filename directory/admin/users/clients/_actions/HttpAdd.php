<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\clients\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpAdd extends EditorBase {

    protected $_showPasswordFields = true;
    protected $_auth;

    protected function _init() {
        $this->_client = $this->data->newRecord('axis://user/Client');
    }

    protected function _setDefaultValues() {
        $locale = $this->i18n->getDefaultLocale();
        
        $this->values->status = 3;
        $this->values->country = $locale->getRegion();
        $this->values->language = $locale->getLanguage();
        $this->values->timezone = $this->i18n->timezones->suggestForCountry($locale->getRegion());
    }

    protected function _prepareRecord() {
        parent::_prepareRecord();

        $this->_auth = $this->data->getModel('user')->auth->newRecord([
            'adapter' => 'Local',
            'identity' => $this->_client['email'],
            'bindDate' => 'now'
        ]);
        
        $this->data->newValidator()
            ->addField('password', 'password')
                ->setMatchField('repeatPassword')
                ->isRequired(true)
                ->end()
            ->validate($this->values)
            ->applyTo($this->_auth);
    }

    protected function _saveRecord() {
        parent::_saveRecord();

        $this->_auth['user'] = $this->_client;
        $this->_auth->save();
    }
}