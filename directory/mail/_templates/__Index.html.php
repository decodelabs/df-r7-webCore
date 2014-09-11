<?php

echo $this->html->collectionList($this['mails'])
    ->addField('name', function($mail, $context) {
        $name = $context->getKey();

        return $this->html->link(
                '~mail/view?path='.$name,
                $name
            )
            ->setIcon('mail')
            ->setDisposition('informative');
    })
    ->addField('description', function($mail) {
        return $mail->getDescription();
    })
    ->addField('templateType', function($mail) {
        return $mail->getTemplateType();
    });