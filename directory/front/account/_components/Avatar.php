<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\account\_components;

use df;
use df\core;
use df\apex;
use df\arch;

class Avatar extends arch\component\template\FormUi {

    protected function _execute($file, array $versions=null) {
        if(!empty($versions)) {
            $form = $this->content->addForm();
            $fs = $form->addFieldSet($this->_('Select existing avatar'));
            $fa = $fs->addFieldArea($this->_('Your images'));
            $activeId = $file['#activeVersion'];

            foreach($versions as $version) {
                $fa->push($this->html('div', [
                        $this->html->tag('input', [
                                'type' => 'image',
                                'src' => $this->uri($version->getImageUrl('[cz:100|100]')),
                                'name' => 'formEvent',
                                'value' => $this->eventName('selectVersion', $version['id'])
                            ])
                            ->setStyle('display', 'block'),

                        $this->html->eventButton($this->eventName('deleteVersion', $version['id']), $this->_('Delete'))
                            ->setIcon('delete')
                    ])
                    ->setStyle('display', 'inline-block')
                    ->setStyle('border', '4px solid #FFF')
                    ->setStyle('padding', '0.5em')
                    ->chainIf($activeId == $version['id'], function($widget) {
                        $widget->addClass('status-active')->setStyle('border', '4px #FEFFBA solid');
                    })
                );
           }
        }


        $form = $this->content->addForm();
        $form->setEncoding($form::ENC_MULTIPART);

        // Upload
        $form->addFieldSet($this->_('Upload new avatar'))->push(
            $this->getDelegate('upload')
        );

        // Buttons
        $form->addButtonArea(
            $this->html->eventButton('upload', $this->_('Upload'))
                ->setIcon('upload')
                ->setDisposition('positive'),

            $this->html->cancelEventButton()
        );
    }
}