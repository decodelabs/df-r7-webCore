<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\users\groups\_nodes;

use df\arch;

class HttpAdd extends arch\node\Form
{
    protected $_group;

    protected function init(): void
    {
        $this->_group = $this->scaffold->newRecord();
    }

    protected function loadDelegates(): void
    {
        $this->loadDelegate('roles', '../roles/RoleSelector');
    }

    protected function createUi(): void
    {
        $model = $this->data->getModel('user');

        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Group details'));

        // Name
        $fs->addField($this->_('Name'))
            ->addTextbox('name', $this->values->name)
                ->setMaxLength(64)
                ->isRequired(true);

        // Signifier
        $fs->addField($this->_('Signifier'))->push(
            $this->html->textbox('signifier', $this->values->signifier)
                ->setMaxLength(32)
        );

        // Roles
        $fs->addField($this->_('Roles'))->push($this['roles']);

        // Buttons
        $fs->addDefaultButtonGroup();
    }

    protected function onSaveEvent()
    {
        $this->data->newValidator()

            // Name
            ->addRequiredField('name', 'text')
                ->setMaxLength(64)

            // Signifier
            ->addField('signifier', 'text')
                ->setMaxLength(32)

            // Roles
            ->addField('roles', 'delegate')
                ->fromForm($this)

            ->validate($this->values)
            ->applyTo($this->_group);


        return $this->complete(function () {
            $this->_group->save();
            $this->user->instigateGlobalKeyringRegeneration();

            $this->comms->flashSaveSuccess('group');
        });
    }
}
