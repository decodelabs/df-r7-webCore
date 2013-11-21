<?php

use df\core;
use df\flow;

echo $this->import->component('DetailHeaderBar', '~devtools/mail/dev/', $this['mail']);

echo $this->html->flashMessage($this->_(
    'This message was received %t% ago',
    ['%t%' => $this->format->timeSince($this['mail']['date'])]
));

if($this['mail']['isPrivate']) {
    echo $this->html->flashMessage($this->_(
        'This message is marked as private'
    ), 'warning');
}


echo $this->html->tag('iframe', [
    'src' => $this->uri->request('~devtools/mail/dev/message?mail='.$this['mail']['id']),
    'seamless' => true,
    'style' => [
        'width' => '70em',
        'height' => '30em'
    ]
]);