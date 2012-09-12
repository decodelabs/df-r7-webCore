<?php
echo $this->html->menuBar()
    ->addLinks(
        $this->html->link(
                $this->uri->request('~devtools/theme/layouts/edit?layout='.$this['layout']->getId(), true),
                $this->_('Edit layout')
            )
            ->setIcon('edit'),

        $this->html->link(
                $this->uri->request(
                    '~devtools/theme/layouts/delete?layout='.$this['layout']->getId(), true,
                    '~devtools/theme/layouts/'
                ),
                $this->_('Delete layout')
            )
            ->setIcon('delete'),

        '|',

        $this->html->backLink()
    );


echo $this->html->attributeList($this['layout'])

    // Id
    ->addField('id', function($layout) {
        return $layout->getId();
    })

    // Name
    ->addField('name', function($layout) {
        return $layout->getName();
    })

    // Areas
    ->addField('areas', function($layout) {
        return implode(', ', $layout->getAreas());
    })

    // Static
    ->addField('isStatic', function($layout) {
        return $this->html->booleanIcon($layout->isStatic());
    });


echo $this->html->element('h3', $this->_('Slots'));

echo $this->html->menuBar()
    ->addLinks(
        $this->html->link(
                $this->uri->request('~devtools/theme/layouts/add-slot?layout='.$this['layout']->getId(), true),
                $this->_('Add new slot')
            )
            ->setIcon('add'),

        $this->html->link(
                $this->uri->request('~devtools/theme/layouts/reorder-slots?layout='.$this['layout']->getId(), true),
                $this->_('Re-order slots')
            )
            ->setIcon('list')
            ->setDisposition('operative'),

        '|',

        $this->html->backLink()
    );



echo $this->html->collectionList($this['layout']->getSlots())
    ->setErrorMessage($this->_('This layout has no slots defined'))

    // Id
    ->addField('id', function($slot) {
        return $this->html->icon('slot', $slot->getId());
    })

    // Name
    ->addField('name', function($slot) {
        return $slot->getName();
    })

    // Min blocks
    ->addField('minBlocks', function($slot) {
        return $slot->getMinBlocks();
    })

    // Max blocks
    ->addField('maxBlocks', function($slot) {
        return $slot->getMaxBlocks();
    })

    // Types
    ->addField('types', function($slot) {
        return implode(', ', $slot->getBlockTypes());
    })

    // Actions
    ->addField('actions', function($slot) {
        return [
            $this->html->link(
                    $this->uri->request('~devtools/theme/layouts/edit-slot?layout='.$this['layout']->getId().'&slot='.$slot->getId(), true),
                    $this->_('Edit')
                )
                ->setIcon('edit'),

            $this->html->link(
                    $this->uri->request('~devtools/theme/layouts/delete-slot?layout='.$this['layout']->getId().'&slot='.$slot->getId(), true),
                    $this->_('Delete')
                )
                ->setIcon('delete')
        ];
    });