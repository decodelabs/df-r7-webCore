<?php
$this->view->setSubject($this->_(
    '%n% has deactivated their account',
    ['%n%' => $user['fullName']]
));

echo $this->html('p', [
    $this->html('strong', $user['fullName']),
    '  has decided to deactivate their account on ',
    $this->application->getName(),
    '.'
]);

if($reason = $deactivation['reason']) {
    echo $this->html('p', [
        $this->html('strong', 'Reason: '),
        $reason
    ]);
}

if($message = $deactivation['comments']) {
    echo $this->html('h4', 'Comments');
    echo $this->html->simpleTags($message);
}
