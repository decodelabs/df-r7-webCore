<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\users\_nodes;

use df;
use df\core;
use df\arch;
use df\user;

class HttpSetupUser extends arch\node\Form {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    protected function initWithSession() {
        $model = $this->data->getModel('user');

        if($model->client->countAll()) {
            throw core\Error::{'EForbidden'}([
                'message' => 'A user has already been set up',
                'http' => 403
            ]);
        }
    }

    protected function setDefaultValues() {
        $this->values->timezone = 'Europe/London';
        $this->values->country = 'GB';
        $this->values->language = 'en';
    }

    protected function createUi() {
        $this->content->push($this->html('p',
            'WARNING: this form wont hold your hand, make sure you type everything properly.. it will also only work ONCE'
        ));

        $form = $this->content->addForm();
        $fs = $form->addFieldSet('User details');

        // Email
        $fs->addField('Email')->push(
            $this->html->emailTextbox('email', $this->values->email)
                ->isRequired(true)
        );

        // Password
        $fs->addField('Password')->push(
            $this->html->passwordTextbox('password', $this->values->password)
                ->isRequired(true)
        );

        // Full name
        $fs->addField('Full name')->push(
            $this->html->textbox('fullName', $this->values->fullName)
                ->isRequired(true)
        );

        // Nick name
        $fs->addField('Nick name')->push(
            $this->html->textbox('nickName', $this->values->nickName)
                ->isRequired(true)
        );

        // Time zone
        $fs->addField('Timezone')->push(
            $this->html->textbox('timezone', $this->values->timezone)
                ->isRequired(true)
        );

        // Country
        $fs->addField('Country')->push(
            $this->html->select('country', $this->values->country, $this->i18n->countries->getList())
                ->isRequired(true)
        );

        // Language
        $fs->addField('Language')->push(
            $this->html->select('language', $this->values->language, $this->i18n->languages->getList())
                ->isRequired(true)
        );

        // Buttons
        $fs->addDefaultButtonGroup();
    }


    protected function onSaveEvent() {
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
                ->extend(function($value, $field) {
                    if(!$this->i18n->timezones->isValidId($value)) {
                        $field->addError('invalid', $this->_(
                            'Please enter a valid timezone id'
                        ));
                    }
                })

            // Country
            ->addRequiredField('country', 'text')
                ->setSanitizer(function($value) {
                    return strtoupper($value);
                })
                ->extend(function($value, $field) {
                    if(!$this->i18n->countries->isValidId($value)) {
                        $field->addError('invalid', $this->_(
                            'Please enter a valid country code'
                        ));
                    }
                })

            // Language
            ->addRequiredField('language', 'text')
                ->setSanitizer(function($value) {
                    return strtolower($value);
                })
                ->extend(function($value, $field) {
                    if(!$this->i18n->languages->isValidId($value)) {
                        $field->addError('invalid', $this->_(
                            'Please enter a valid language id'
                        ));
                    }
                })

            ->validate($this->values);


        return $this->complete(function() use($validator) {
            $model = $this->data->getModel('user');
            $model->installDefaultManifest();

            $client = $model->client->newRecord();

            $validator->applyTo($client, [
                'email', 'fullName', 'nickName',
                'timezone', 'country', 'language'
            ]);

            $auth = $model->auth->newRecord([
                'user' => $client,
                'adapter' => 'Local'
            ]);

            $validator->getField('email')->setRecordName('identity');
            $validator->applyTo($auth, ['email', 'password']);

            $client->groups->add('77abfc6a-bab7-c3fa-f701-e08615a46c35');
            $auth->save();

            return $this->http->redirect('account/login');
        });
    }
}
