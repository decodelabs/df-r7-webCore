<?php


use DecodeLabs\Genesis;
use DecodeLabs\Metamorph;
use DecodeLabs\Tagged as Html;

$this->view->setSubject($this->_(
    'Come and join us at %n%',
    ['%n%' => Genesis::$hub->getApplicationName()]
));

echo $generator->p(function () use ($invite) {
    if ($invite['owner']) {
        yield Html::strong($invite['owner']->getFullName());
        yield ' has invited you';
    } else {
        yield 'You have been invited';
    }

    yield ' to become a member at ';
    yield Html::strong(Genesis::$hub->getApplicationName());
});

if ($message = $invite['message']) {
    echo Metamorph::idiom($message);
}

echo $generator->p([
    $generator->link(
        $this->uri('account/register?invite=' . $invite['key']),
        'Please follow this link to set up your account'
    )
]);
