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

use DecodeLabs\Tagged as Html;
use DecodeLabs\Exceptional;

class HttpReorderSlots extends arch\node\Form
{
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    protected $_layout;

    protected function init()
    {
        $config = fire\Config::getInstance();

        if (!$this->_layout = $config->getLayoutDefinition($this->request['layout'])) {
            throw Exceptional::{'df/fire/layout/NotFound'}([
                'message' => 'Layout not found',
                'http' => 404
            ]);
        }
    }

    protected function getInstanceId()
    {
        return $this->_layout->getId();
    }

    protected function setDefaultValues()
    {
        $i = 1;

        foreach ($this->_layout->getSlots() as $slot) {
            $this->values->slots[] = [
                'id' => $slot->getId(),
                'weight' => $i
            ];

            $i++;
        }
    }

    protected function createUi()
    {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Slot order'));
        $fa = $fs->addField();

        foreach ($this->values->slots as $i => $slotNode) {
            if (!$slot = $this->_layout->getSlot($slotNode['id'])) {
                continue;
            }

            $fa->push(
                Html::{'div'}([
                    $this->html->numberTextbox('slots['.$i.'][weight]', $slotNode->weight)
                        ->setStep(1)
                        ->isRequired(true),

                    $this->html->hidden('slots['.$i.'][id]', $slot->getId()),

                    $slot->getName()
                ])
            );
        }

        $fa->push(
            $this->html->eventButton($this->eventName('refresh'), $this->_('Update form'))
                ->setIcon('refresh')
                ->setDisposition('informative')
        );

        $fs->addDefaultButtonGroup();
    }

    protected function onRefreshEvent()
    {
        $queue = new \SplPriorityQueue();

        foreach ($this->values->slots as $slotNode) {
            $queue->insert($slotNode['id'], $slotNode->get('weight', 10000));
        }

        $this->values->remove('slots');
        $i = count($queue);

        foreach ($queue as $id) {
            if (!$slot = $this->_layout->getSlot($id)) {
                continue;
            }

            $this->values->slots->unshift([
                'id' => $slot->getId(),
                'weight' => $i
            ]);

            $i--;
        }
    }

    protected function onSaveEvent()
    {
        $this->onRefreshEvent();

        return $this->complete(function () {
            $ids = [];

            foreach ($this->values->slots as $slotNode) {
                $ids[] = $slotNode['id'];
            }

            $this->_layout->setSlotOrder($ids);

            $config = fire\Config::getInstance();
            $config->setLayoutDefinition($this->_layout)->save();

            $this->comms->flashSuccess(
                'slot.reorder',
                $this->_('The layout slots have been successfully reordered')
            );
        });
    }
}
