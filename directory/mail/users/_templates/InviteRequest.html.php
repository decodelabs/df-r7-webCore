<?php
$this->view->setTitle($this->_(
    'New invite request from %n%',
    ['%n%' => $this['request']['name']]
));

echo $this->html('p', [
    $this->html('strong', $this['request']['name']),
    ', ',
    $this['request']['companyPosition'],
    ' of ',
    $this['request']['companyName'],
    ' has asked to become a member at ',
    $this->html('strong', $this->application->getName())
]);

if($message = $this['invite']['message']) {
    echo $this->html->simpleTags($message);
}

echo $this->html('p', [
    $this->html->basicLink(
            '~admin/users/invite-requests/respond?request='.$this['request']['id'],
            'Please follow this link to see more details and respond'
        )
        ->setAttribute('target', '_blank')
]);
