<?php
$this->view->setSubject($this->_(
    'Your invite request at %n%',
    ['%n%' => $this->context->application->getName()]
));

echo $this->html('p', [
    'Sorry, but your request to join ',
    $this->html('strong', $this->application->getName()),
    ' has been turned down.'
]);

if($message = $this['message']) {
    echo $this->html->simpleTags($message);
}