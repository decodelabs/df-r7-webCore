<?php

use DecodeLabs\Genesis;
use DecodeLabs\Tagged as Html;

if ($user->isNew()) {
    echo $generator->h4([
        'You have a new account on ',
        Genesis::$hub->getApplicationName()
    ]);
} else {
    echo $generator->h4([
        'Your email address has recently changed on ',
        Genesis::$hub->getApplicationName()
    ]);
}

echo $generator->p([
    'To make sure your details are correct and we can contact you, ',
    Html::br(),
    $generator->link(
        $this->uri('account/email-verify?key='.$key.'&user='.$user['id']),
        'please verify this email address'
    ),
    '.'
]);
