<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\users\_actions;

use df;
use df\core;
use df\arch;
use df\user;

class HttpSetupUser extends arch\form\Action {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;
    
    protected function _onSessionReady() {
        $model = $this->data->getModel('user');
        
        if($model->client->countAll()) {
            $this->throwError(403, 'A user has already been set up');
        }
    }
    
    protected function _setDefaultValues() {
        $this->values->timezone = 'Europe/London';
        $this->values->country = 'GB';
        $this->values->language = 'en';
    }
    
    protected function _createUi() {
        $this->content->push($this->html(
                '<p>WARNING: this form wont hold your hand, make sure you type everything properly.. it will also only work ONCE</p>'
        ));
        
        $this->content->addForm()->push(
            $this->html->fieldSet('User details')->push(
            
                // Email
                $this->html->fieldArea('Email')->push(
                    $this->html->emailTextbox(
                            'email', $this->values->email
                        )
                        ->isRequired(true)
                ),
                
                // Password
                $this->html->fieldArea('Password')->push(
                    $this->html->passwordTextbox(
                            'password', $this->values->password
                        )
                        ->isRequired(true)
                ),
                
                // Full name
                $this->html->fieldArea('Full name')->push(
                    $this->html->textbox(
                            'fullName', $this->values->fullName
                        )
                        ->isRequired(true)
                ),
                
                // Nick name
                $this->html->fieldArea('Nick name')->push(
                    $this->html->textbox(
                            'nickName', $this->values->nickName
                        )
                        ->isRequired(true)
                ),
                
                // Time zone
                $this->html->fieldArea('Timezone')->push(
                    $this->html->textbox(
                            'timezone', $this->values->timezone
                        )
                        ->isRequired(true)
                ),
                
                // Country
                $this->html->fieldArea('Country')->push(
                    $this->html->selectList(
                            'country',
                            $this->values->country,
                            $this->i18n->countries->getList()
                        )
                        ->isRequired(true)
                ),
                
                // Language
                $this->html->fieldArea('Language')->push(
                    $this->html->selectList(
                            'language',
                            $this->values->language,
                            $this->i18n->languages->getList()
                        )
                        ->isRequired(true)
                ),
                
                
                // Buttons
                $this->html->buttonArea(
                    $this->html->eventButton(
                            $this->eventName('save'), 
                            $this->_('Save')
                        )
                        ->setIcon('save'),
                        
                    $this->html->eventButton(
                            $this->eventName('reset'), 
                            $this->_('Reset')
                        )
                        ->shouldValidate(false)
                        ->setIcon('refresh'),

                    $this->html->eventButton(
                            $this->eventName('cancel'),
                            $this->_('Cancel')
                        )
                        ->shouldValidate(false)
                        ->setIcon('cancel')
                )
            )
        );
    }


    protected function _onSaveEvent() {
        $validator = $this->data->newValidator()

            // Email
            ->addRequiredField('email')

            // Password
            ->addRequiredField('password', 'password')

            // Full name
            ->addRequiredField('fullName', 'text')

            // Nick name
            ->addRequiredField('nickName', 'text')

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

            ->validate($this->values);

        if($this->values->isValid()) {
            $model = $this->data->getModel('user');

            $model->installDefaultManifest();

            $client = $model->client->newRecord();
            $validator->applyTo($client, [
                'email', 'fullName', 'nickName', 'timezone', 'country', 'language'
            ]);

            $auth = $model->auth->newRecord([
                'user' => $client,
                'adapter' => 'Local'
            ]);
            $validator->getField('email')->setRecordName('identity');
            $validator->applyTo($auth, ['email', 'password']);
            
            $client->groups->add('77abfc6a-bab7-c3fa-f701-e08615a46c35');
            $auth->save();
            
            $this->complete();
            
            return $this->http->redirect('account/login');
        }
    }
}
