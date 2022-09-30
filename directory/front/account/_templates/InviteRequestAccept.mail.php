<?php

use DecodeLabs\Genesis;
use DecodeLabs\Metamorph;
use DecodeLabs\Tagged as Html;

$this->view->setSubject($this->_(
    'Your invite request at %n%',
    ['%n%' => Genesis::$hub->getApplicationName()]
));

echo $generator->p([
    'Your request to join ',
    Html::string(Genesis::$hub->getApplicationName()),
    ' has been accepted and your account is now active.'
]);

if ($message = $this['message']) {
    echo Metamorph::idiom($message);
}
