<?php

echo $this->import->template('elements/Header.html');

echo $this->html->collectionList($this['client']->authDomains->fetch())
    ->setErrorMessage($this->_('There are no authentication entries to display'))

    // Adapter
    ->addField('adapter')

    // Identity
    ->addField('identity')

    // Bind date
    ->addField('bindDate', function($auth) {
        return $this->html->date($auth['bindDate']);
    })

    // Login date
    ->addField('loginDate', $this->_('Last login'), function($auth) {
        if($auth['loginDate']) {
            return $this->html->timeSince($auth['loginDate']);
        }
    })

    // Actions
    ->addField('actions', function($auth) {
        if($auth['adapter'] == 'Local') {
            return $this->html->link(
                    $this->uri->request('~admin/users/change-password?user='.$auth->getRawId('user'), true),
                    $this->_('Change password')
                )
                ->setIcon('edit')
                ->setDisposition('operative');
        }
    });