<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\media\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\neon;
use df\halo;

class HttpTransfer extends arch\form\Action {
    
    const DEFAULT_EVENT = 'transfer';

    protected function createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Transfer media library'));

        $handlerList = neon\mediaHandler\Base::getEnabledHandlerList();
        $current = neon\mediaHandler\Base::getInstance();
        unset($handlerList[$current->getName()]);

        if(!empty($handlerList)) {
            $fs->addFieldArea()->addFlashMessage(
                    $this->_('Are you sure you want to transfer your media library? This process can take a LONG time and some files may not be available during transfer'), 
                    'warning'
                )
                ->setDescription($this->_(
                    'During the transfer you should try to avoid making any changes to your media library otherwise data may be lost or fall out of sync - please be patient!'
                ));
        }

        // From
        $fs->addFieldArea($this->_('From'))->push(
            $this->html->textbox('from', $current->getDisplayName())
                ->isDisabled(true)
        );

        // To
        $fa = $fs->addFieldArea($this->_('To'));

        if(empty($handlerList)) {
            $fa->addFlashMessage($this->_(
                'There are currently no other available file stores to transfer to'
            ), 'error');

            $fa->addButtonArea($this->html->cancelEventButton());
            return;
        }

        $fa->push(
            $this->html->selectList('to', $this->values->to, $handlerList)
                ->setNoSelectionLabel(null)
                ->isRequired(true)
        );

        // Delete
        $fs->addFieldArea()->push(
            $this->html->checkbox('deleteSource', $this->values->deleteSource, $this->_(
                'Delete files from source once transferred'
            ))
        );

        // Buttons
        $fs->addDefaultButtonGroup('transfer', $this->_('Transfer'));
    }

    protected function onTransferEvent() {
        $handlerList = neon\mediaHandler\Base::getEnabledHandlerList();
        $current = neon\mediaHandler\Base::getInstance();
        unset($handlerList[$current->getName()]);

        $validator = $this->data->newValidator()
            ->addRequiredField('to', 'enum')
                ->setOptions(array_keys($handlerList))

            ->addField('deleteSource', 'boolean')

            ->validate($this->values);

        return $this->complete(function() use($validator) {
            $task = 'media/transfer?to='.$validator['to'];

            if($validator['deleteSource']) {
                $task .= '&delete';
            }

            return $this->task->initiateStream($task);
        });
    }
}