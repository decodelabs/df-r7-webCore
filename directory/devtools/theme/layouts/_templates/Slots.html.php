<?php

echo $this->apex->component('~devtools/theme/layouts/DetailHeaderBar', $layout);

echo $this->html->collectionList($slotList)
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
    ->addField('category', function($slot) {
        return $slot->getCategory();
    })

    // Actions
    ->addField('actions', function($slot) use($layout) {
        return [
            $this->html->link(
                    $this->uri('~devtools/theme/layouts/edit-slot?layout='.$layout->getId().'&slot='.$slot->getId(), true),
                    $this->_('Edit')
                )
                ->setIcon('edit'),

            $this->html->link(
                    $this->uri('~devtools/theme/layouts/delete-slot?layout='.$layout->getId().'&slot='.$slot->getId(), true),
                    $this->_('Delete')
                )
                ->setIcon('delete')
        ];
    });