<?php
echo $this->html->collectionList($entryList->getEntries())
    ->setErrorMessage($this->_('This menu has no entries'))

    // Id
    ->addField('id', function ($entry) {
        return $entry->getId();
    })

    // Type
    ->addField('type', function ($entry) {
        return $entry->getType();
    })

    // Weight
    ->addField('weight', function ($entry) {
        return $entry->getWeight();
    })

    // Preview
    ->addField('preview', function ($entry) {
        switch ($entry->getType()) {
            case 'Link':
                return $this->html->link($entry);

            case 'Spacer':
                return Html::{'hr'}();
        }
    })

    // Description
    ->addField('description')
    ;
