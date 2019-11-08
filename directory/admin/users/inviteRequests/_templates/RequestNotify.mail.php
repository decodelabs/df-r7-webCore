<?php
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
    Html::strong($this->app->getName())
]);

if ($message = $request['message']) {
    echo $this->html->simpleTags($message);
}

echo $generator->p([
    $generator->link(
        $this->uri('~admin/users/invite-requests/respond?request='.$request['id']),
        'Please follow this link to see more details and respond'
    )
]);
