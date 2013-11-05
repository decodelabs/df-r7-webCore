<?php

echo $this->import->component('DetailHeaderBar', '~admin/users/invites/', $this['invite']);


if(!$this['invite']['isActive']) {
    echo $this->html->flashMessage($this->_(
        'This invite is no longer active'
    ), 'warning');
}

echo $this->html->attributeList($this['invite'])
    ->addField('key')
    ->addField('creationDate', function($invite) {
        return $this->html->date($invite['creationDate']);
    })
    ->addField('owner', function($invite) {
        return $this->import->component('UserLink', '~admin/users/', $invite['owner']);
    })
    ->addField('lastSent', function($invite) {
        return $this->html->userDateTime($invite['lastSent']);
    })
    ->addField('name')
    ->addField('email', function($invite) {
        return $this->html->mailLink($invite['email']);
    })
    ->addField('message', function($invite) {
        return $this->html->simpleTags($invite['message']);
    })
    ->addField('groups', function($invite) {
        return $this->html->bulletList($invite->groups->fetch(), function($group) {
            return $this->import->component('GroupLink', '~admin/users/groups/', $group);
        });
    })
    ->addField('registrationDate', function($invite) {
        return $this->html->userDateTime($invite['registrationDate']);
    })
    ->addField('user', $this->_('Registered account'), function($invite) {
        return $this->import->component('UserLink', '~admin/users/', $invite['user'])
            ->isNullable(true);
    });