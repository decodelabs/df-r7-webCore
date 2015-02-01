<?php

// Menu
echo $this->apex->component('IndexHeaderBar');


// Form
$form = $this->html->form()->setMethod('get');
$fs = $form->addFieldSet($this->_('Filter'))->push(
    $this->html->selectList(
            'area',
            $this['areaFilter'],
            $this['areaList']
        )
        ->setNoSelectionLabel(null),

    $this->html->submitButton(
            null,
            $this->_('Go')
        )
        ->setDisposition(true)
        ->setIcon('search')
);

echo $form;


// Collection
echo $this->apex->component('MenuList');