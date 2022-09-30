<?php

use DecodeLabs\Genesis;
use DecodeLabs\Metamorph;
use DecodeLabs\Tagged as Html;

$this->view->setSubject($this->_(
    '%n% has deactivated their account',
    ['%n%' => $user['fullName']]
));

echo $generator->p([
    Html::strong($user['fullName']),
    '  has decided to deactivate their account on ',
    Genesis::$hub->getApplicationName(),
    '.'
]);

if ($reason = $deactivation['reason']) {
    echo $generator->p([
        Html::strong('Reason: '),
        $reason
    ]);
}

if ($message = $deactivation['comments']) {
    echo $generator->h4('Comments');
    echo Metamorph::idiom($message);
}
