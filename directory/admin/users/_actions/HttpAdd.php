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
    
class HttpAdd extends arch\form\Action {

    const DEFAULT_EVENT = 'save';

    protected $_client;
    protected $_isNew = false;
    
    protected function _init() {
        $model = $this->data->getModel('user');
        $this->_client = $model->client->newRecord();
        $this->_isNew = true;
        
        // TODO: check access
    }
    
    protected function _setupDelegates() {
        $this->loadDelegate('groups', 'GroupSelector', '~admin/users/groups/');
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
        if($this->_isNew) {
            $fs = $form->addFieldSet($this->_('Password'));
            
            $fs->addFieldArea($this->_('Password'))
                ->addPasswordTextbox('password', $this->values->password)
                    ->isRequired(true);
                    
            $fs->addFieldArea($this->_('Repeat password'))
                ->addPasswordTextbox('repeatPassword', $this->values->repeatPassword)
                    ->isRequired(true);
        }


        // Groups
        $form->push($this->getDelegate('groups')->renderFieldSet());
                
        
        // Buttons
        $form->push($this->html->defaultButtonGroup());
    }

    protected function _onSaveEvent() {
        $model = $this->data->getModel('user');
        $context = $this->_context;
        $client = $this->_client;
        
        $this->data->newValidator()
            ->shouldSanitize(true)
            ->addField('email', 'email')
                ->setCustomValidator(function($node, $value) use ($model, $context, $client) {
                    if($client['email'] == $value) {
                        return;
                    }
                        
                    if($model->client->select()->where('email', '=', $value)->count()) {
                        $node->addError('unique', $context->_(
                            'This email address is already in use by another account'
                        ));
                    }
                })
                ->isRequired(true)
                ->end()
            ->addField('fullName', 'text')
                ->isRequired(true)
                ->end()
            ->addField('nickName', 'text')
                ->isRequired(true)
                ->end()
            ->addField('status', 'integer')
                ->setCustomValidator(function($node, $value) use ($context) {
                    if($value < -1 || $value > 3) {
                        $node->addError('invalid', $context->_(
                            'Please enter a valid status id'
                        ));
                    }
                })
                ->isRequired(true)
                ->end()
            ->addField('timezone', 'text')
                ->setSanitizer(function($value) {
                    return str_replace(' ', '/', ucwords(str_replace('/', ' ', $value)));
                })
                ->setCustomValidator(function($node, $value) use ($context) {
                    if(!$context->i18n->timezones->isValidId($value)) {
                        $node->addError('invalid', $context->_(
                            'Please enter a valid timezone id'
                        ));
                    }
                })
                ->isRequired(true)
                ->end()
            ->addField('country', 'text')
                ->setSanitizer(function($value) {
                    return strtoupper($value);
                })
                ->setCustomValidator(function($node, $value) use ($context) {
                    if(!$context->i18n->countries->isValidId($value)) {
                        $node->addError('invalid', $context->_(
                            'Please enter a valid country code'
                        ));
                    }
                })
                ->isRequired(true)
                ->end()
            ->addField('language', 'text')
                ->setSanitizer(function($value) {
                    return strtolower($value);  
                })
                ->setCustomValidator(function($node, $value) use ($context) {
                    if(!$context->i18n->languages->isValidId($value)) {
                        $node->addError('invalid', $context->_(
                            'Please enter a valid langauge id'
                        ));
                    }
                })
                ->isRequired(true)
                ->end()
            ->validate($this->values)
            ->applyTo($this->_client);
            
        $this->_client->groups = $this->getDelegate('groups')->getGroupIds();
        
        
        if($this->_isNew) {
            $auth = $model->auth->newRecord(array(
                'adapter' => 'Local',
                'identity' => $this->_client['email'],
                'bindDate' => 'now'
            ));
            
            $this->data->newValidator()
                ->addField('password', 'password')
                    ->setMatchField('repeatPassword')
                    ->isRequired(true)
                    ->end()
                ->validate($this->values)
                ->applyTo($auth);
        }
        
        if($this->isValid()) {
            $this->_client->save();
            
            if($this->_isNew) {
                $auth['user'] = $this->_client;
                $auth->save();
            } else {
                $model->auth->update(array(
                        'identity' => $this->_client['email']
                    ))
                    ->where('user', '=', $this->_client)
                    ->where('adapter', '=', 'Local')
                    ->execute()
                    ;
            }
            
            $this->arch->notify(
                'user.saved', 
                $this->_('The user account has been successfully saved'), 
                'success'
            );
            
            return $this->complete();
        }
    }
}