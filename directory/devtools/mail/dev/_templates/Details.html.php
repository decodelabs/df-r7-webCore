<?php

echo $this->import->component('IndexHeaderBar', '~devtools/mail/dev/', $this['mail']);

echo $this->html->attributeList($this['mail'])
    
    // Subject
    ->addField('subject')

    // From
    ->addField('from', function($mail) {
        if(!$from = $mail->getFromAddress()) {
            return null;
        }

        return $this->html->link(
                $this->uri->mailto($from->getAddress()),
                $from
            )
            ->setIcon('user');
    })

    // To
    ->addField('to', function($mail) {
        return $this->html->bulletList($mail->getToAddresses(), function($address) {
            return $this->html->link(
                    $this->uri->mailto($address->getAddress()),
                    $address
                )
                ->setIcon('user');
        });
    })

    // Date
    ->addField('date', $this->_('Sent'), function($mail) {
        return $this->html->userDateTime($mail['date'], 'medium');
    })

    // Is private
    ->addField('isPrivate', $this->_('Private'), function($mail) {
        return $this->html->lockIcon($mail['isPrivate']);
    })
    ;



echo $this->html->container(
    $this->html->notification($this->_('Sorry, mime messages can\'t currently be rendered.. here is the original message body'), 'debug'),

    $this->html->element(
        'pre',
        $this['mail']['body']
    )
);