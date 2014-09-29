<?php

echo $this->html->collectionList($this['mails'])
    ->addField('name', function($mail, $context) {
        $name = $context->getKey();

        if(!$mail) {
            return $this->html->element('span.error', $this->html->icon('mail', $name));
        }

        return $this->html->link(
                '~mail/templates/view?path='.$name,
                $name
            )
            ->setIcon('mail')
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
    });