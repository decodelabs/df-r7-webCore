<?php
$this->view->setSubject($this->_(
    'Come and join us at %n%',
    ['%n%' => $this->app->getName()]
));

echo $this->html('p', [
    $this->html('strong', $invite['owner']->getFullName()),
    ' has invited you to become a member at ',
    $this->html('strong', $this->app->getName())
]);

if($message = $invite['message']) {
    echo $this->html->simpleTags($message);
}

echo $this->html('p', [
    $this->html->basicLink('account/register?invite='.$invite['key'], 'Please follow this link to set up your account')
        ->setAttribute('target', '_blank')
]);
