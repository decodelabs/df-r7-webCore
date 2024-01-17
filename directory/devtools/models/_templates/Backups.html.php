<?php

use DecodeLabs\Genesis;
use DecodeLabs\Tagged as Html;

echo $this->apex->component('~devtools/models/IndexHeaderBar');

echo $this->html->collectionList($backupList)
    ->addField('name', function ($backup) {
        return $this->html->link(
            $this->uri('~devtools/models/download-backup?backup=' . $backup, true),
            $backup
        )
            ->setIcon('download');
    })
    ->addField('created', function ($backup) {
        return Html::$time->since(\df\core\time\Date::fromCompressedString(substr($backup, -18, 14), 'UTC'));
    })
    ->addField('size', function ($backup) {
        return Html::$number->fileSize(filesize(Genesis::$hub->getSharedDataPath() . '/backup/' . $backup));
    })
    ->addField('actions', function ($backup) {
        return [
            $this->html->link(
                $this->uri('~devtools/models/delete-backup?backup=' . $backup, true),
                $this->_('Delete backup')
            )
                ->setIcon('delete')
        ];
    });
