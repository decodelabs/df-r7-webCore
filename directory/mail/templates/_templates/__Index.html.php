<?php

echo $this->html->collectionList($this['mails'])
    ->addField('name', function($mail, $context) {
        $name = $context->getKey();

        if(!$mail) {
            return $this->html('span.error', $this->html->icon('mail', $name));
        }

        return $this->html->link(
                '~mail/templates/view?path='.$name,
                $name
            )
            ->setIcon('theme')
            ->setDisposition('informative');
    })
    ->addField('description', function($mail) {
        if($mail) {
            return $mail->getDescription();
        }
    })
    ->addField('templateType', function($mail) {
        if($mail) {
            return $mail->getTemplateType();
        }
    })
    ->addField('actions', function($mail, $context) {
        if($mail) {
            return $this->html->link(
                    $this->uri('~mail/templates/preview?path='.$context->key, true),
                    $this->_('Send preview')
                )
                ->setIcon('mail')
                ->setDisposition('positive');
        }
    });