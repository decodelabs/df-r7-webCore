<?php
$this->view->setSubject($this->_(
    'Your invite request at %n%',
    ['%n%' => $this->app->getName()]
));

echo $this->html('p', [
    'Sorry, but your request to join ',
    $this->html('strong', $this->app->getName()),
    ' has been turned down.'
]);

if($message = $this['message']) {
    echo $this->html->simpleTags($message);
}
