<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\theme\layouts\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;
use df\fire;
    
class HttpAdd extends arch\form\Action {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    protected $_layout;

    protected function init() {
        $this->_layout = new fire\layout\Definition();
    }

    protected function createUi() {
        $form = $this->content->addForm();
        $detailsFs = $form->addFieldSet($this->_('Layout details'));

        // Id
        $detailsFs->addFieldArea($this->_('Id'))->push(
            $this->html->textbox('id', $this->values->id)
                ->setPattern('[A-Z][a-zA-Z0-9]*')
                ->isRequired(!$this->_layout->isStatic())
                ->isDisabled($this->_layout->isStatic())
        );

        // Name
        $detailsFs->addFieldArea($this->_('Name'))->push(
            $this->html->textbox('name', $this->values->name)
                ->isRequired(true)
        );

        // Areas
        $detailsFs->addFieldArea($this->_('Areas'))->setDescription($this->_(
            'Separate with commas, leave blank for all'
        ))
        ->push(
            $this->html->textbox('areas', $this->values->areas)
                ->setPlaceholder('eg. front, admin, devtools')
                ->isDisabled($this->_layout->isStatic())
        );

        $detailsFs->addDefaultButtonGroup();
    }

    protected function onSaveEvent() {
        $origId = $this->_layout->getId();

        $this->data->newValidator()

            // Id
            ->addField('id', 'text')
                ->setSanitizer(function($value) {
                    return ucfirst($value);
                })
                ->setPattern('/^[A-Z][a-zA-Z0-9]*$/')
                ->isRequired(!$this->_layout->isStatic())

            // Name
            ->addRequiredField('name', 'text')

            // Areas
            ->addField('areas', 'text')
                ->setSanitizer(function($value) {
                    $parts = explode(',', trim($value));

                    foreach($parts as $i => $part) {
                        $parts[$i] = ltrim(trim($part), arch\Request::AREA_MARKER);
                    }

                    return implode(', ', $parts);
                })

            ->validate($this->values);

        if($this->isValid()) {
            $config = fire\Config::getInstance();

            if(!$this->_layout->isStatic() && $config->isStaticLayout($this->values['id'])) {
                $this->values->id->addError('static', $this->_(
                    'This is is already in use by a static layout'
                ));
            }
        }

        return $this->complete(function() use($origId, $config) {
            if($origId !== null && $origId !== $this->values['id']) {
                $config->removeLayoutDefinition($origId);
            }

            if(!$this->_layout->isStatic()) {
                $this->_layout->setId($this->values['id']);
                $areas = $this->values['areas'];

                if(!empty($areas)) {
                    $this->_layout->setAreas(explode(', ', $areas));
                }
            }

            $this->_layout->setName($this->values['name']);

            $config->setLayoutDefinition($this->_layout)->save();
            $this->comms->flashSaveSuccess('layout');
        });
    }
}