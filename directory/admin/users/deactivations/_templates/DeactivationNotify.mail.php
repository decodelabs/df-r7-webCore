<?php
use DecodeLabs\Tagged\Html;

$this->view->setSubject($this->_(
    '%n% has deactivated their account',
    ['%n%' => $user['fullName']]
));

echo $generator->p([
    Html::strong($user['fullName']),
    '  has decided to deactivate their account on ',
    $this->app->getName(),
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
    echo $this->html->simpleTags($message);
}
