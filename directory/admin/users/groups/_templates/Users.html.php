<?php

// Header
echo $this->import->component('DetailHeaderBar', '~admin/users/groups/', $this['group']);

// Users
echo $this->html->element('h3', $this->_('Users'));

echo $this->import->component('UserList', '~admin/users/');