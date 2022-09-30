<?php

use DecodeLabs\Genesis;
use DecodeLabs\Metamorph;
use DecodeLabs\Tagged as Html;

$this->view->setSubject($this->_(
    'Your invite request at %n%',
    ['%n%' => Genesis::$hub->getApplicationName()]
));

echo $generator->p([
    'Sorry, but your request to join ',
    Html::strong(Genesis::$hub->getApplicationName()),
    ' has been turned down.'
]);

if ($message = $this['message']) {
    echo Metamorph::idiom($message);
}
