<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\theme\layouts\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;
use df\fire;

use DecodeLabs\Glitch;

class HttpAddSlot extends arch\node\Form
{
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    protected $_layout;
    protected $_slot;

    protected function init()
    {
        $config = fire\Config::getInstance();

        if (!$this->_layout = $config->getLayoutDefinition($this->request['layout'])) {
            throw Glitch::{'df/fire/layout/ENotFound'}([
                'message' => 'Layout not found',
                'http' => 404
            ]);
        }

        $this->_slot = new fire\slot\Definition('__default__');
    }

    protected function getInstanceId()
    {
        return $this->_layout->getId().':'.$this->_slot->getId();
    }

    protected function createUi()
    {
        $form = $this->content->addForm();
        $detailsFs = $form->addFieldSet($this->_('Slot details'));

        // Layout
        $detailsFs->addField($this->_('Layout'))->push(
            $this->html->textbox('layout', $this->_layout->getName())
                ->isDisabled(true)
        );

        // Id
        $detailsFs->addField($this->_('Id'))->push(
            $this->html->textbox('id', $this->values->id)
                ->isRequired(true)
                ->isReadOnly($this->_slot->isPrimary())
        );

        // Name
        $detailsFs->addField($this->_('Name'))->push(
            $this->html->textbox('name', $this->values->name)
                ->isRequired(true)
        );

        // Min blocks
        $detailsFs->addField($this->_('Min blocks'))->push(
            $this->html->numberTextbox('minBlocks', $this->values->minBlocks)
                ->setRange(0, null, 1)
        );

        // Max blocks
        $detailsFs->addField($this->_('Max blocks'))->push(
            $this->html->numberTextbox('maxBlocks', $this->values->maxBlocks)
                ->setRange(1, null, 1)
        );


        // Block types
        $detailsFs->addField($this->_('Block category'))->push(
            // TODO: add block type selector
            $this->html->flashMessage('Block type selector coming soon...', 'debug')
        );

        // Buttons
        $detailsFs->addDefaultButtonGroup();
    }


    protected function onSaveEvent()
    {
        $this->data->newValidator()

            // Id
            ->addRequiredField('id', 'text')
                ->setPattern('/^[a-zA-Z0-9]+$/')

            // Name
            ->addRequiredField('name', 'text')

            // Min blocks
            ->addField('minBlocks', 'integer')
                ->setMin(0)

            // Max blocks
            ->addField('maxBlocks', 'integer')
                ->setMin(1)

            // Block types
            //->addField('blockTypes')

            ->validate($this->values)
                ;


        $config = fire\Config::getInstance();

        if ($this->isValid()) {
            if ($this->values['id'] !== $this->_slot->getId()) {
                if ($this->_layout->getSlot($this->values['id'])) {
                    $this->values->id->addError('unique', $this->_(
                        'There is already a slot with that id'
                    ));
                } elseif ($this->_slot->isPrimary()) {
                    $this->values->id->addError('static', $this->_(
                        'The primary slot is required so you cannot change this id'
                    ));
                }
            }
        }

        return $this->complete(function () use ($config) {
            $this->_layout->removeSlot($this->_slot->getId());

            $this->_slot->setId($this->values['id'])
                ->setName($this->values['name'])
                ->setMinBlocks($this->values['minBlocks'])
                ->setMaxBlocks($this->values['maxBlocks'])
                //->setBlockTypes($this->values->blockTypes->toArray())
                ;

            $this->_layout->addSlot($this->_slot);
            $config->setLayoutDefinition($this->_layout)->save();
            $this->comms->flashSaveSuccess('slot');
        });
    }
}
