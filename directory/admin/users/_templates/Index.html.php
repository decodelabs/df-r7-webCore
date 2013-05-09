<?php

echo $this->import->component('IndexHeaderBar', '~admin/users/');


echo $this->html->form('~admin/users/')->setMethod('get')->push(
    $this->html->fieldSet($this->_('Search'))->push(
        $this->html->searchTextbox('search', $this['search']),
        $this->html->submitButton(null, $this->_('Go'))
            ->setIcon('search')
            ->setDisposition('positive')
    )
);


echo $this->import->component('UserList', '~admin/users/');