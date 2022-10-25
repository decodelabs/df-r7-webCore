<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\processes\daemons\_nodes;

use df\core;
use df\arch;

class HttpSettings extends arch\node\Form
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;

    protected $_config;
    protected $_isRecord = false;
    protected $_names = [
        'isEnabled' => 'daemonsEnabled',
        'user' => 'daemonUser',
        'group' => 'daemonGroup'
    ];

    protected function init(): void
    {
        if (isset($this->request['daemon'])) {
            $this->_isRecord = true;

            $this->_config = $this->data->fetchOrCreateForAction(
                'axis://daemon/Settings',
                $this->request['daemon'],
                function ($settings) {
                    $settings['name'] = $this->request['daemon'];
                }
            );
        } else {
            $this->_config = core\environment\Config::getInstance();
        }
    }

    protected function getInstanceId(): ?string
    {
        if ($this->_isRecord) {
            return $this->_config['name'];
        }

        return null;
    }

    protected function setDefaultValues(): void
    {
        $this->values->importFrom($this->_config, [
            $this->_getFieldName('isEnabled'),
            $this->_getFieldName('user'),
            $this->_getFieldName('group')
        ]);
    }

    protected function createUi(): void
    {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Daemon settings'));

        // Enabled
        $fs->addField()->push(
            $this->html->checkbox($this->_getFieldName('isEnabled'), $this->values->{$this->_getFieldName('isEnabled')},
                $this->_isRecord ?
                    $this->_('This daemon is enabled and can be spawned by this application') :
                    $this->_('Allow this application to spawn deamon processes')
            )
        );

        $env = core\environment\Config::getInstance();

        // User
        $fs->addField($this->_('User'))->push(
            $this->html->textbox($this->_getFieldName('user'), $this->values->{$this->_getFieldName('user')})
                ->isRequired(!$this->_isRecord)
                ->setPlaceholder($env->getDaemonUser())
        );

        // Group
        $fs->addField($this->_('Group'))->push(
            $this->html->textbox($this->_getFieldName('group'), $this->values->{$this->_getFieldName('group')})
                ->isRequired(!$this->_isRecord)
                ->setPlaceholder($env->getDaemonGroup())
        );

        // Buttons
        $fs->addDefaultButtonGroup();
    }

    protected function onSaveEvent()
    {
        $this->data->newValidator()

            // Enabled
            ->addRequiredField($this->_getFieldName('isEnabled'), 'boolean')

            // User
            ->addField($this->_getFieldName('user'), 'text')
                ->isRequired(!$this->_isRecord)
                ->extend(function ($value, $field) {
                    // TODO: test user
                })

            // Group
            ->addField($this->_getFieldName('group'), 'text')
                ->isRequired(!$this->_isRecord)
                ->extend(function ($value, $field) {
                    // TODO: test group
                })

            ->validate($this->values)
            ->applyTo($this->_config);


        return $this->complete(function () {
            $this->_config->save();

            $this->comms->flashSuccess(
                'daemonConfig.save',
                $this->_('Your settings have been successfully saved')
            );
        });
    }

    protected function _getFieldName($key)
    {
        if ($this->_isRecord || !isset($this->_names[$key])) {
            return $key;
        }

        return $this->_names[$key];
    }
}
