<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\processes\daemons\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpSettings extends arch\form\Action {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    protected $_config;

    protected function _init() {
        $this->_config = core\Environment::getInstance();
    }

    protected function _setDefaultValues() {
        $this->values->importFrom($this->_config, [
            'daemonsEnabled', 'daemonUser', 'daemonGroup'
        ]);
    }

    protected function _createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Daemon settings'));

        // Enabled
        $fs->addFieldArea()->push(
            $this->html->checkbox('daemonsEnabled', $this->values->daemonsEnabled, $this->_(
                'Allow this application to spawn deamon processes'
            ))
        );

        // User
        $fs->addFieldArea($this->_('User'))->push(
            $this->html->textbox('daemonUser', $this->values->daemonUser)
                ->isRequired(true)
        );

        // Group
        $fs->addFieldArea($this->_('Group'))->push(
            $this->html->textbox('daemonGroup', $this->values->daemonGroup)
                ->isRequired(true)
        );

        // Buttons
        $fs->push($this->html->defaultButtonGroup());
    }

    protected function _onSaveEvent() {
        $this->data->newValidator()

            // Enabled
            ->addRequiredField('daemonsEnabled', 'boolean')

            // User
            ->addRequiredField('daemonUser', 'text')
                ->setCustomValidator(function($node, $value) {

                })

            // Group
            ->addRequiredField('daemonGroup', 'text')
                ->setCustomValidator(function($node, $value) {

                })

            ->validate($this->values)
            ->applyTo($this->_config->values);

        if($this->isValid()) {
            $this->_config->save();

            $this->comms->flash(
                'daemonConfig.save',
                $this->_('Your settings have been successfully saved'),
                'success'
            );

            return $this->complete();
        }
    }
}