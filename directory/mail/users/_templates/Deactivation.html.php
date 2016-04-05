<?php
$this->view->setTitle($this->_(
    '%n% has deactivated their account',
    ['%n%' => $this['client']['fullName']]
));

echo $this->html('p', [
    $this->html('strong', $this['client']['fullName']),
    '  has decided to deactivate their account on ',
    $this->application->getName(),
    '.'
]);

if($reason = $this['deactivation']['reason']) {
    echo $this->html('p', [
        $this->html('strong', 'Reason: '),
        $reason
    ]);
}

if($message = $this['deactivation']['comments']) {
    echo $this->html('h4', 'Comments');
    echo $this->html->simpleTags($message);
}
