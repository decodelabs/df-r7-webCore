<?php
use df\core;

echo $this->import->component('DetailHeaderBar', '~devtools/mail/dev/', $this['mail']);

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



echo $this->html->elementContentContainer(function() {
    $parts = $this['message']->getParts();

    $renderer = function(array $parts) use(&$renderer) {
        $output = [];

        foreach($parts as $part) {
            if($part instanceof core\mime\IMultiPart) {
                $output[] = $this->html->container(
                    $this->html->attributeList($part->getHeaders()->toArray())->setStyle('font-size', '0.8em'),
                    $renderer($part->getParts())
                );
            } else if($part instanceof core\mime\IContentPart) {
                $content = [$this->html->attributeList($part->getHeaders()->toArray())->setStyle('font-size', '0.8em')];

                switch($part->getContentType()) {
                    case 'text/plain':
                        $content[] = $this->html->element('div', $this->html->plainText($part->getContent()));
                        break;

                    case 'text/html':
                        $content[] = $this->html->element('div', $this->html->string($part->getContent()));
                        break;
                }

                $output[] = $this->html->container($content);
            }
        }        

        return $output;
    };

    return $renderer($parts);
});