<?php


use DecodeLabs\Genesis;
use DecodeLabs\Metamorph;
use DecodeLabs\Tagged as Html;

$this->view->setSubject($this->_(
    'Come and join us at %n%',
    ['%n%' => Genesis::$hub->getApplicationName()]
));

echo $generator->p([
    Html::strong($invite['owner']->getFullName()),
    ' has invited you to become a member at ',
    Html::strong(Genesis::$hub->getApplicationName())
]);

if ($message = $invite['message']) {
    echo Metamorph::idiom($message);
}

echo $generator->p([
    $generator->link(
        $this->uri('account/register?invite=' . $invite['key']),
        'Please follow this link to set up your account'
    )
]);
