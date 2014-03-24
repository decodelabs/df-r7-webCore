<?php

// Header
echo $this->import->component('DetailHeaderBar', '~admin/system/critical-errors/', $this['error']);


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

    // User agent
    ->addField('userAgent')

    // Frequency
    ->addField('frequency', function($error) {
        return $this->_('This error has been seen %n% times', ['%n%' => $error->fetchFrequency()]);
    })

    // Exception type
    ->addField('exceptionType')

    // File
    ->addField('file', function($error) {
        return $error['file'].' : '.$error['line'];
    })
    
    // Message
    ->addField('message', function($error) {
        return $this->html->element('code', $error['message']);
    })
    ;

if($this['error']['stackTrace']) {
    echo $this->html->element('h3', $this->_('Stack trace'));

    $trace = json_decode($this['error']['stackTrace'], true);

    echo $this->html->collectionList($trace)
        ->addField('file', function($call) {
            if($call['file']) {
                return $call['file'].' : '.$call['line'];
            }
        })
        ->addField('signature', function($call) {
            return $call['signature'];
        });
}