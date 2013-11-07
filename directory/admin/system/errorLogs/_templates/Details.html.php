<?php

// Header
echo $this->import->component('DetailHeaderBar', '~admin/system/error-logs/', $this['log']);


// Details
echo $this->html->attributeList($this['log'])
    
    // Date
    ->addField('date', function($log) {
        return $this->html->dateTime($log['date']);
    })

    // Code
    ->addField('code', function($log) {
        $icon = 'info';

        if($log['code'] == 404) {
            $icon = 'warning';
        } else if($log['code'] == 500) {
            $icon = 'error';
        }

        return $this->html->icon($icon, $log['code'])
            ->addClass('state-'.$icon);
    })

    // User
    ->addField('user', function($log) {
        return $this->import->component('UserLink', '~admin/users/clients/', $log['user'])
            ->isNullable(false)
            ->setDisposition('transitive');
    })

    // Production
    ->addField('isProduction', $this->_('Production mode'), function($log) {
        return $this->html->booleanIcon($log['isProduction']);
    })

    // Request
    ->addField('request', function($log) {
        if($log['request']) {
            return $this->html->link($log['request'], explode('://', $log['request'])[1]);
        }
    })

    // Frequency
    ->addField('frequency', function($log) {
        return $this->_('This error has been seen %n% times', ['%n%' => $log->fetchFrequency()]);
    })

    // Exception type
    ->addField('exceptionType')

    // Message
    ->addField('message', function($log) {
        return $this->html->plainText($log['message']);
    })
    ;