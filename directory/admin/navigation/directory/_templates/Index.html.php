<?php
// Menu
echo $this->html->menuBar()
    ->addLinks(
        $this->html->link(
                $this->uri->request('~admin/navigation/directory/refresh', true),
                $this->_('Refresh menu list')
            )
            ->setIcon('refresh'),

        '|',

        $this->html->backLink()
    );



// Form
$form = $this->html->form()->setMethod('get');
$fs = $form->addFieldSet($this->_('Filter'))->push(
    $this->html->label($this->_('Area')),

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