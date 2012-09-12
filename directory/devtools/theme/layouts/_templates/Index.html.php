<?php
echo $this->html->menuBar()
	->addLinks(
        $this->html->link(
        		$this->uri->request('~devtools/theme/layouts/add', true),
        		$this->_('Add new layout')
            )
            ->setIcon('add'),

        '|',

        $this->html->backLink()
	);


echo $this->html->collectionList($this['layoutList'])
    ->setErrorMessage($this->_('There are currently no layouts to display'))

    // Id
    ->addField('id', function($layout) {
        return $this->html->link(
                '~devtools/theme/layouts/details?layout='.$layout->getId(),
                $layout->getId()
            )
            ->setId('layout')
            ->setDisposition('informative');
    })

    // Name
    ->addField('name', function($layout) {
        return $layout->getName();
    })

    // Areas
    ->addField('areas', function($layout) {
        return implode(', ', $layout->getAreas());
    })

    // Slots
    ->addField('slots', function($layout) {
        return $layout->countSlots();
    })

    // Static
    ->addField('isStatic', $this->_('Static'), function($layout) {
        return $this->html->booleanIcon($layout->isStatic());
    })

    // Actions
    ->addField('actions', function($layout) {
        return [
            $this->html->link(
                    $this->uri->request('~devtools/theme/layouts/edit?layout='.$layout->getId(), true),
                    $this->_('Edit')
                )
                ->setIcon('edit'),

            $this->html->link(
                    $this->uri->request('~devtools/theme/layouts/delete?layout='.$layout->getId(), true),
                    $this->_('Delete')
                )
                ->setIcon('delete')
        ];
    })
    ;