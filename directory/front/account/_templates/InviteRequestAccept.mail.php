<?php
use DecodeLabs\Tagged as Html;

$this->view->setSubject($this->_(
    'Your invite request at %n%',
    ['%n%' => $this->app->getName()]
));

echo $generator->p([
    'Your request to join ',
    Html::string($this->app->getName()),
    ' has been accepted and your account is now active.'
]);

if ($message = $this['message']) {
    echo $this->html->simpleTags($message);
}
