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
    
class HttpAdd extends arch\form\Action {

    protected $_client;

    protected function _init() {
        $this->_client = $this->scaffold->newRecord();
    }

    protected function _setupDelegates() {
        $this->loadDelegate('groups', '../groups/GroupSelector');
    }

    protected function _setDefaultValues() {
        $locale = $this->i18n->getDefaultLocale();
        
        $this->values->status = 3;
        $this->values->country = $locale->getRegion();
        $this->values->language = $locale->getLanguage();
        $this->values->timezone = $this->i18n->timezones->suggestForCountry($locale->getRegion());
    }
    
    protected function _createUi() {
        $model = $this->data->getModel('user');
        
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('User details'));
        
        // Email
        $fs->addFieldArea($this->_('Email'))
            ->addEmailTextbox('email', $this->values->email)
                ->isRequired(true);
                
        // Full name
        $fs->addFieldArea($this->_('Full name'))
            ->addTextbox('fullName', $this->values->fullName)
                ->isRequired(true);
                
        // Nickname
        $fs->addFieldArea($this->_('Nickname'))
            ->addTextbox('nickName', $this->values->nickName)
                ->isRequired(true);
                
        // Status
        $fs->addFieldArea($this->_('Status'))
            ->addSelectList('status', $this->values->status, [
                    -1 => $this->_('Deactivated'),
                    0 => $this->_('Guest'),
                    1 => $this->_('Pending'),
                    2 => $this->_('Bound'),
                    3 => $this->_('Confirmed')
                ])
                ->isRequired(true);
        
                
        // Time zone
        $fs->addFieldArea('Timezone')
            ->addSelectList('timezone', $this->values->timezone)
                ->setOptions($this->i18n->timezones->getList(), true)
                ->isRequired(true);
        
        // Country
        $fs->addFieldArea('Country')
            ->addSelectList(
                    'country',
                    $this->values->country,
                    $this->i18n->countries->getList()
                )
                ->isRequired(true);
        
        // Language
        $fs->addFieldArea('Language')
                ->addSelectList(
                    'language',
                    $this->values->language,
                    $this->i18n->languages->getList()
                )
                ->isRequired(true);
                
                
                
        // Password
        if($this->_client->isNew()) {
            $fs = $form->addFieldSet($this->_('Password'));
            
            $fs->addFieldArea($this->_('Password'))
                ->addPasswordTextbox('password', $this->values->password)
                    ->isRequired(true);
                    
            $fs->addFieldArea($this->_('Repeat password'))
                ->addPasswordTextbox('repeatPassword', $this->values->repeatPassword)
                    ->isRequired(true);
        }


        // Groups
        $fs->addFieldArea($this->_('Groups'))->push(
            $this->getDelegate('groups')
        );
                
        
        // Buttons
        $fs->addDefaultButtonGroup();
    }


    protected function _onSaveEvent() {
        $isNew = $this->_client->isNew();

        $this->data->newValidator()

            // Email
            ->addRequiredField('email')
                ->setRecord($this->_client)
                ->setUniqueErrorMessage($this->_('This email address is already in use by another account'))

            // Full name
            ->addRequiredField('fullName', 'text')

            // Nick name
            ->addRequiredField('nickName', 'text')

            // Status
            ->addRequiredField('status', 'integer')
                ->setCustomValidator(function($node, $value) {
                    if($value < -1 || $value > 3) {
                        $node->addError('invalid', $this->_(
                            'Please enter a valid status id'
                        ));
                    }
                })

            // Groups
            ->addField('groups', 'delegate')
                ->fromForm($this)

            // Timezone
            ->addRequiredField('timezone', 'text')
                ->setSanitizer(function($value) {
                    return str_replace(' ', '/', ucwords(str_replace('/', ' ', $value)));
                })
                ->setCustomValidator(function($node, $value) {
                    if(!$this->i18n->timezones->isValidId($value)) {
                        $node->addError('invalid', $this->_(
                            'Please enter a valid timezone id'
                        ));
                    }
                })

            // Country
            ->addRequiredField('country', 'text')
                ->setSanitizer(function($value) {
                    return strtoupper($value);
                })
                ->setCustomValidator(function($node, $value) {
                    if(!$this->i18n->countries->isValidId($value)) {
                        $node->addError('invalid', $this->_(
                            'Please enter a valid country code'
                        ));
                    }
                })
                
            // Language
            ->addRequiredField('language', 'text')
                ->setSanitizer(function($value) {
                    return strtolower($value);  
                })
                ->setCustomValidator(function($node, $value) {
                    if(!$this->i18n->languages->isValidId($value)) {
                        $node->addError('invalid', $this->_(
                            'Please enter a valid language id'
                        ));
                    }
                })

            ->validate($this->values)
            ->applyTo($this->_client);


        if($isNew) {
            $auth = $this->data->getModel('user')->auth->newRecord([
                'adapter' => 'Local',
                'identity' => $this->_client['email'],
                'bindDate' => 'now'
            ]);
            
            $this->data->newValidator()
                ->addRequiredField('password')
                    ->setMatchField('repeatPassword')
                ->validate($this->values)
                ->applyTo($auth);
        }
        
        if($this->isValid()) {
            $this->_client->save();

            if($isNew) {
                $auth['user'] = $this->_client;
                $auth->save();
            }

            $this->comms->flashSaveSuccess('user');
            return $this->complete();
        }
    }
}