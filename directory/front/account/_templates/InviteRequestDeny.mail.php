<?php
use DecodeLabs\Tagged\Html;

$this->view->setSubject($this->_(
    'Your invite request at %n%',
    ['%n%' => $this->app->getName()]
));

echo $generator->p([
    'Sorry, but your request to join ',
    Html::strong($this->app->getName()),
    ' has been turned down.'
]);

if ($message = $this['message']) {
    echo $this->html->simpleTags($message);
}
