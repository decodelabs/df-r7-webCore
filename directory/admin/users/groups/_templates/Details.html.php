<?php

// Header
echo $this->import->component('DetailHeaderBar', '~admin/users/groups/', $this['group']);

// Roles
echo $this->html->element('h3', $this->_('Roles'));

echo $this->import->component('RoleList', '~admin/users/roles/', [
        'actions' => false
    ]);