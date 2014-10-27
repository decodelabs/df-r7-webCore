<?php

echo $this->import->component('~devtools/models/IndexHeaderBar');

echo $this->html->collectionList($this['backupList'])
    ->addField('name', function($backup) {
        return $backup;
    })
    ->addField('created', function($backup) {
        return $this->html->timeFromNow(\df\core\time\Date::fromCompressedString(substr($backup, 5, -4), 'UTC'));
    })
    ->addField('actions', function($backup) {
        return [
            $this->html->link(
                    $this->uri('~devtools/models/restore-backup?backup='.$backup, true),
                    $this->_('Restore backup')
                )
                ->setIcon('import')
                ->setDisposition('operative'),

            $this->html->link(
                    $this->uri('~devtools/models/delete-backup?backup='.$backup, true),
                    $this->_('Delete backup')
                )
                ->setIcon('delete')
        ];
    });