<?php

// Header
echo $this->import->component('DetailHeaderBar', '~admin/system/not-found/', $this['error']);


// Details
echo $this->html->attributeList($this['error'])
    
    // Date
    ->addField('date', function($error) {
        return $this->html->dateTime($error['date']);
    })


    // User
    ->addField('user', function($error) {
        return $this->import->component('UserLink', '~admin/users/clients/', $error['user'])
            ->isNullable(true)
            ->setDisposition('transitive');
    })

    // Mode
    ->addField('mode', function($error) {
        return [
            $error['mode'], ' ',
            $this->html->element('sup', '('.($error['isProduction'] ? $this->_('production') : $this->_('testing')).')')
                ->addClass($error['isProduction'] ? 'state-error' : 'state-warning')
        ];
    })

    // Request
    ->addField('request', function($error) {
        if($error['request']) {
            return $this->html->link($error['request'], explode('://', $error['request'])[1]);
        }
    })

    // Referrer
    ->addField('referrer', function($error) {
        if($error['referrer']) {
            return $this->html->link($error['referrer']);
        }
    })

    // Frequency
    ->addField('frequency', function($error) {
        return $this->_('This error has been seen %n% times', ['%n%' => $error->fetchFrequency()]);
    })

    // Message
    ->addField('message', function($error) {
        return $this->html->element('code', $error['message']);
    })
    ;