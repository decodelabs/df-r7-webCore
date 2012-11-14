<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
abstract class EditorBase extends arch\form\Action {

    const DEFAULT_EVENT = 'save';

    protected $_client;
    protected $_showPasswordFields = false;

    protected function _setupDelegates() {
        $this->loadDelegate('groups', 'GroupSelector', '~admin/users/groups/');
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
            ->addSelectList('status', $this->values->status, array(
                    -1 => $this->_('Deactivated'),
                    0 => $this->_('Guest'),
                    1 => $this->_('Pending'),
                    2 => $this->_('Bound'),
                    3 => $this->_('Confirmed')
                ))
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
        if($this->_showPasswordFields) {
            $fs = $form->addFieldSet($this->_('Password'));
            
            $fs->addFieldArea($this->_('Password'))
                ->addPasswordTextbox('password', $this->values->password)
                    ->isRequired(true);
                    
            $fs->addFieldArea($this->_('Repeat password'))
                ->addPasswordTextbox('repeatPassword', $this->values->repeatPassword)
                    ->isRequired(true);
        }


        // Groups
        $form->push($this->getDelegate('groups')->renderFieldSet($this->_('Groups')));
                
        
        // Buttons
        $form->push($this->html->defaultButtonGroup());
    }


    protected function _onSaveEvent() {
        $this->data->newValidator()

            // Email
            ->addField('email', 'email')
                ->setCustomValidator(function($node, $value) {
                    if($this->_client['email'] == $value) {
                        return;
                    }
                        
                    if($this->data->user->client->select()->where('email', '=', $value)->count()) {
                        $node->addError('unique', $this->_(
                            'This email address is already in use by another account'
                        ));
                    }
                })
                ->isRequired(true)
                ->end()

            // Full name
            ->addField('fullName', 'text')
                ->isRequired(true)
                ->end()

            // Nick name
            ->addField('nickName', 'text')
                ->isRequired(true)
                ->end()

            // Status
            ->addField('status', 'integer')
                ->setCustomValidator(function($node, $value) {
                    if($value < -1 || $value > 3) {
                        $node->addError('invalid', $this->_(
                            'Please enter a valid status id'
                        ));
                    }
                })
                ->isRequired(true)
                ->end()

            // Timezone
            ->addField('timezone', 'text')
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
                ->isRequired(true)
                ->end()

            // Country
            ->addField('country', 'text')
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
                ->isRequired(true)
                ->end()
                
            // Language
            ->addField('language', 'text')
                ->setSanitizer(function($value) {
                    return strtolower($value);  
                })
                ->setCustomValidator(function($node, $value) {
                    if(!$this->i18n->languages->isValidId($value)) {
                        $node->addError('invalid', $this->_(
                            'Please enter a valid langauge id'
                        ));
                    }
                })
                ->isRequired(true)
                ->end()

            ->validate($this->values)
            ->applyTo($this->_client);


        if($this->isValid()) {
            $this->_prepareRecord();
            $this->_saveRecord();

            $this->arch->notify(
                'client.save',
                $this->_('The user has been successfully saved'),
                'success'
            );

            return $this->complete();
        }
    }

    protected function _prepareRecord() {
        $this->_client->groups = $this->getDelegate('groups')->apply();
    }

    protected function _saveRecord() {
        $this->_client->save();
    }
}