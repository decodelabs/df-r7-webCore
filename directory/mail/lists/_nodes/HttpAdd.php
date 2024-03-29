<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\mail\lists\_nodes;

use df\arch;
use df\core;
use df\flow;

class HttpAdd extends arch\node\Form
{
    protected $_manager;

    protected function init(): void
    {
        $this->_manager = flow\Manager::getInstance();
    }

    protected function createUi(): void
    {
        if (!$adapter = $this->getStore('adapter')) {
            $this->createAdapterUi();
            return;
        }

        $this->createOptionsUi();
    }

    protected function createAdapterUi()
    {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Mailing list adapter'));

        // Adapter
        $fs->addField($this->_('Adapter'))->push(
            $this->html->select('adapter', $this->values->adapter, $this->_manager->getAvailableListAdapters())
                ->isRequired(true)
        );

        // Buttons
        $fs->addDefaultButtonGroup('setAdapter', $this->_('Select'));
    }

    protected function createOptionsUi()
    {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Mailing list adapter'));

        // Adapter
        $adapter = $this->getStore('adapter');
        $fs->addField($this->_('Adapter'))->push(
            $this->html->textbox('adapter', $adapter)
                ->isDisabled(true)
        );

        // Id
        $fs->addField($this->_('Id'))->push(
            $this->html->textbox('id', $this->values->id)
                ->isRequired(true)
        );

        // Options
        $options = $this->_manager->getListAdapterSettingsFields($adapter);

        foreach ($options as $option => $optionName) {
            $fs->addField($optionName)->push(
                $this->html->autoField($option, $optionName, $this->values)
            );
        }

        // Primary
        if ($this->hasStore('options')) {
            $options = new core\collection\Tree($this->getStore('options'));
            $options['adapter'] = $adapter;

            $id = $this->values->get('id', uniqid());

            try {
                $source = new flow\mailingList\Source($id, $options);
                $lists = $source->getListOptions();
            } catch (\Throwable $e) {
                $lists = [];
            }

            $fs->addField($this->_('Primary list'))->push(
                $this->html->select('primaryList', $this->values->primaryList, $lists)
            );
        }


        // Buttons
        if (!$this->hasStore('options')) {
            $fs->addDefaultButtonGroup('setOptions', $this->_('Update'));
        } else {
            $fs->addDefaultButtonGroup();
        }
    }

    protected function onSetAdapterEvent()
    {
        $validator = $this->data->newValidator()
            ->addRequiredField('adapter', 'enum')
                ->setOptions($this->_manager->getAvailableListAdapters())
            ->validate($this->values);

        if ($validator->isValid()) {
            $this->setStore('adapter', $validator['adapter']);
            $options = $this->_manager->getListAdapterSettingsFields($validator['adapter']);

            if (empty($options)) {
                $this->setStore('options', []);
            }
        }
    }

    protected function onSetOptionsEvent()
    {
        if (!$adapter = $this->getStore('adapter')) {
            return;
        }

        $options = $this->_manager->getListAdapterSettingsFields($adapter);
        $validator = $this->data->newValidator();

        foreach ($options as $option => $name) {
            $validator->addAutoField($option);
        }

        $validator->validate($this->values);

        if ($validator->isValid()) {
            $options = new core\collection\Tree($validator->getValues());
            $options['adapter'] = $adapter;

            $id = $this->values->get('id', uniqid());

            try {
                $source = new flow\mailingList\Source($id, $options);
                $source->getManifest();
            } catch (\Throwable $e) {
                $this->values->adapter->addError('invalid', $this->_(
                    'The adapter cannot connect'
                ));

                $this->removeStore('options');
                return;
            }

            $this->setStore('options', $validator->getValues());
        } else {
            $this->removeStore('options');
        }
    }

    protected function onSaveEvent()
    {
        if (!$adapter = $this->getStore('adapter')) {
            return;
        }

        if (!$options = $this->getStore('options')) {
            return;
        }

        $this->onSetOptionsEvent();

        $validator = $this->data->newValidator()
            ->addRequiredField('id', 'text')
            ->addField('primaryList', 'text')
            ->validate($this->values);


        return $this->complete(function () use ($adapter, $options, $validator) {
            $options = array_merge(
                ['adapter' => $adapter],
                $options,
                ['primaryList' => $validator['primaryList']]
            );

            $this->comms->flashSaveSuccess('source');
        });
    }
}
