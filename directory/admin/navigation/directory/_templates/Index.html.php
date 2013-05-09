<?php

// Menu
echo $this->import->component('IndexHeaderBar', '~admin/navigation/directory/');


// Form
$form = $this->html->form()->setMethod('get');
$fs = $form->addFieldSet($this->_('Filter'))->push(
    $this->html->selectList(
            'area',
            $this['areaFilter'],
            $this['areaList']
        ),

    $this->html->submitButton(
            null,
            $this->_('Go')
        )
        ->setDisposition(true)
        ->setIcon('search')
);

echo $form;


// Collection
echo $this['facetController']->list->renderTo($this);