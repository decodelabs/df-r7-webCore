<?php

// Header
echo $this->import->template('elements/Header.html');

// Users
echo $this->html->element('h3', $this->_('Users'));

echo $this->import->component('UserList', '~admin/users/');