<?php
use DecodeLabs\Tagged\Html;

$this->view->setSubject($this->_(
    'Come and join us at %n%',
    ['%n%' => $this->app->getName()]
));

echo $generator->p([
    Html::strong($invite['owner']->getFullName()),
    ' has invited you to become a member at ',
    Html::strong($this->app->getName())
]);

if ($message = $invite['message']) {
    echo $this->html->simpleTags($message);
}

echo $generator->p([
    $generator->link(
        $this->uri('account/register?invite='.$invite['key']),
        'Please follow this link to set up your account'
    )
]);
