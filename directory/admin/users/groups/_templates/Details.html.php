<?php

// Header
echo $this->import->template('elements/Header.html');

// Roles
echo $this->html->element('h3', $this->_('Roles'));

echo $this->import->component('RoleList', '~admin/users/roles/', [
        'actions' => false
    ]);