<?php

use DecodeLabs\Genesis;
use DecodeLabs\Metamorph;
use DecodeLabs\Tagged as Html;

$this->view->setSubject($this->_(
    'New invite request from %n%',
    ['%n%' => $request['name']]
));

echo $generator->p([
    Html::strong($request['name']),
    ', ',
    $request['companyPosition'],
    ' of ',
    $request['companyName'],
    ' has asked to become a member at ',
    Html::strong(Genesis::$hub->getApplicationName())
]);

if ($message = $request['message']) {
    echo Metamorph::idiom($message);
}

echo $generator->p([
    $generator->link(
        $this->uri('~admin/users/invite-requests/respond?request='.$request['id']),
        'Please follow this link to see more details and respond'
    )
]);
