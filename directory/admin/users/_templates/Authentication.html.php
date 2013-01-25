<?php

echo $this->import->template('elements/Header.html');

echo $this->html->collectionList($this['client']->authDomains->fetch())
    ->setErrorMessage($this->_('There are no authentication entries to display'))

    // Adapter
    ->addField('adapter')

    // Identity
    ->addField('identity')

    // Bind date
    ->addField('bindDate', function($row) {
        return $this->format->date($row['bindDate']);
    })

    // Login date
    ->addField('loginDate', $this->_('Last login'), function($row) {
        if($row['loginDate']) {
            return $this->format->timeSince($row['loginDate']);
        }
    });