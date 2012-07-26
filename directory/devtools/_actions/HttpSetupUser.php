<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\_actions;

use df;
use df\core;
use df\arch;
use df\user;

class HttpSetupUser extends arch\form\Action {
    
    const DEFAULT_ACCESS = user\IState::DEV;
    
    protected function _onSessionCreate() {
        $model = $this->data->getModel('user');
        
        if($model->client->select()->count()) {
            $this->throwError(403, 'A user has already been set up');
        }
    }
    
    protected function _setDefaultValues() {
        $this->values->timezone = 'Europe/London';
        $this->values->country = 'GB';
        $this->values->language = 'en';
    }
    
    protected function _createUi() {
        $this->content->push($this->html->string(
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
        if($this->values->isValid()) {
            $model = $this->data->getModel('user');
            $client = $model->client->newRecord($this->values->toArray());
            $auth = $model->auth->newRecord(array(
                'user' => $client,
                'adapter' => 'Local',
                'identity' => $client['email'],
                'password' => $this->data->hash($this->values['password'])
            ));
            
            $group = $model->group->newRecord(array(
                'name' => 'Developers'
            ));
            
            $client->groups->add($group);
            
            $role = $model->role->newRecord(array(
                'name' => 'Super user',
                'minRequiredState' => user\IState::CONFIRMED,
                'priority' => 99999
            ));
            
            $group->roles->add($role);
            
            $auth->save();
            
            $key = $model->key->newRecord(array(
                'role' => $role['id'],
                'domain' => 'directory',
                'pattern' => '~admin/*',
                'allow' => true
            ));
            
            $key->save();
            $this->complete();
            
            return $this->http->redirect('account/login');
        }
    }
}
