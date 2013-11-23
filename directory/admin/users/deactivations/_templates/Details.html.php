<?php

echo $this->import->component('DetailHeaderBar', '~admin/users/deactivations/', $this['deactivation']);

echo $this->html->attributeList($this['deactivation'])
    ->addField('user', function($deactivation) {
        return $this->import->component('UserLink', '~admin/users/clients/', $deactivation['user']);
    })
    ->addField('date', function($deactivation) {
        return $this->html->userDate($deactivation['date']);
    })
    ->addField('reason')
    ->addField('comments', function($deactivation) {
        return $this->html->plainText($deactivation['comments']);
    })
    ;
