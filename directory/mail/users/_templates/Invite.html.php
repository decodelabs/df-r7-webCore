<?php
$this->view->setTitle($this->_(
    'Come and join us at %n%',
    ['%n%' => $this->context->application->getName()]
));

echo $this->html('p', [
    $this->html('strong', $this['invite']['owner']->getFullName()),
    ' has invited you to become a member at ',
    $this->html('strong', $this->application->getName())
]);

if($message = $this['invite']['message']) {
    echo $this->html->simpleTags($message);
}

echo $this->html('p', [
    $this->html->basicLink('account/register?invite='.$this['invite']['key'], 'Please follow this link to set up your account')->setAttribute('target', '_blank')
]);