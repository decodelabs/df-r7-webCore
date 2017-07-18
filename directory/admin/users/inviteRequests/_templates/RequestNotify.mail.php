<?php
$this->view->setSubject($this->_(
    'New invite request from %n%',
    ['%n%' => $request['name']]
));

echo $this->html('p', [
    $this->html('strong', $request['name']),
    ', ',
    $request['companyPosition'],
    ' of ',
    $request['companyName'],
    ' has asked to become a member at ',
    $this->html('strong', $this->app->getName())
]);

if($message = $request['message']) {
    echo $this->html->simpleTags($message);
}

echo $this->html('p', [
    $this->html->basicLink(
            '~admin/users/invite-requests/respond?request='.$request['id'],
            'Please follow this link to see more details and respond'
        )
        ->setAttribute('target', '_blank')
]);
