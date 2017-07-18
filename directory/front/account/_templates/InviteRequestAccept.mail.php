<?php
$this->view->setSubject($this->_(
    'Your invite request at %n%',
    ['%n%' => $this->app->getName()]
));

echo $this->html('p', [
    'Your request to join ',
    $this->html('strong', $this->app->getName()),
    ' has been accepted and your account is now active.'
]);

if($message = $this['message']) {
    echo $this->html->simpleTags($message);
}

echo $this->html('p', $this->html->link('/'));
