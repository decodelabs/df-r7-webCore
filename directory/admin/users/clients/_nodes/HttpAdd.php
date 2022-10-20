<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\users\clients\_nodes;

use df\arch;

class HttpAdd extends arch\node\Form
{
    protected $_client;

    protected function init()
    {
        $this->_client = $this->scaffold->newRecord();
    }

    protected function loadDelegates(): void
    {
        $this->loadDelegate('groups', '../groups/GroupSelector');
    }

    protected function setDefaultValues(): void
    {
        $locale = $this->i18n->getDefaultLocale();

        $this->values->status = 3;
        $this->values->country = $locale->getRegion();
        $this->values->language = $locale->getLanguage();
        $this->values->timezone = $this->i18n->timezones->suggestForCountry($locale->getRegion());
    }

    protected function createUi()
    {
        $model = $this->data->getModel('user');

        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('User details'));

        // Email
        $fs->addField($this->_('Email'))
            ->addEmailTextbox('email', $this->values->email)
                ->isRequired(true);

        // Full name
        $fs->addField($this->_('Full name'))
            ->addTextbox('fullName', $this->values->fullName)
                ->isRequired(true);

        // Nickname
        $fs->addField($this->_('Nickname'))
            ->addTextbox('nickName', $this->values->nickName)
                ->isRequired(true);

        // Status
        $fs->addField($this->_('Status'))
            ->addSelect('status', $this->values->status, [
                    -2 => $this->_('Spam account'),
                    -1 => $this->_('Deactivated'),
                    //0 => $this->_('Guest'),
                    1 => $this->_('Pending'),
                    //2 => $this->_('Bound'),
                    3 => $this->_('Confirmed')
                ])
                ->isRequired(true);


        // Time zone
        $fs->addField('Timezone')
            ->addSelect('timezone', $this->values->timezone)
                ->setOptions($this->i18n->timezones->getList(), true)
                ->isRequired(true);

        // Country
        $fs->addField('Country')
            ->addSelect(
                    'country',
                    $this->values->country,
                    $this->i18n->countries->getList()
                )
                ->isRequired(true);

        // Language
        $fs->addField('Language')
                ->addSelect(
                    'language',
                    $this->values->language,
                    $this->i18n->languages->getList()
                )
                ->isRequired(true);



        // Password
        if ($this->_client->isNew()) {
            $fs = $form->addFieldSet($this->_('Password'));

            $fs->addField($this->_('Password'))
                ->addPasswordTextbox('password', $this->values->password)
                    ->isRequired(true);

            $fs->addField($this->_('Repeat password'))
                ->addPasswordTextbox('repeatPassword', $this->values->repeatPassword)
                    ->isRequired(true);
        }


        // Groups
        $fs->addField($this->_('Groups'))->push($this['groups']);


        // Buttons
        $fs->addDefaultButtonGroup();
    }


    protected function onSaveEvent()
    {
        $isNew = $this->_client->isNew();
        $auth = null;

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
                ->extend(function ($value, $field) {
                    if (!in_array($value, [-2, -1, 1, 3], true)) {
                        $field->addError('invalid', $this->_(
                            'Please enter a valid status id'
                        ));
                    }
                })

            // Groups
            ->addField('groups', 'delegate')
                ->fromForm($this)

            // Timezone
            ->addRequiredField('timezone', 'text')
                ->setSanitizer(function ($value) {
                    return str_replace(' ', '/', ucwords(str_replace('/', ' ', $value)));
                })
                ->extend(function ($value, $field) {
                    if (!$this->i18n->timezones->isValidId($value)) {
                        $field->addError('invalid', $this->_(
                            'Please enter a valid timezone id'
                        ));
                    }
                })

            // Country
            ->addRequiredField('country', 'text')
                ->setSanitizer(function ($value) {
                    return strtoupper($value);
                })
                ->extend(function ($value, $field) {
                    if (!$this->i18n->countries->isValidId($value)) {
                        $field->addError('invalid', $this->_(
                            'Please enter a valid country code'
                        ));
                    }
                })

            // Language
            ->addRequiredField('language', 'text')
                ->setSanitizer(function ($value) {
                    return strtolower($value);
                })
                ->extend(function ($value, $field) {
                    if (!$this->i18n->languages->isValidId($value)) {
                        $field->addError('invalid', $this->_(
                            'Please enter a valid language id'
                        ));
                    }
                })

            ->validate($this->values)
            ->applyTo($this->_client);


        if ($isNew) {
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

        return $this->complete(function () use ($isNew, $auth) {
            $this->_client->save();

            if ($isNew) {
                $auth['user'] = $this->_client;
                $auth->save();
            }

            $this->comms->flashSaveSuccess('user');
        });
    }
}
