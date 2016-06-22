<?php
$this->view->setSubject($this->_(
    'Your invite request at %n%',
    ['%n%' => $this->context->application->getName()]
));

echo $this->html('p', [
    'Your request to join ',
    $this->html('strong', $this->application->getName()),
    ' has been accepted and your account is now active.'
]);

if($message = $this['message']) {
    echo $this->html->simpleTags($message);
}

echo $this->html('p', $this->html->link('/'));