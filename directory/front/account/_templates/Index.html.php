<?php 
$client = $this->context->user->client; 

echo $this->html->element('p', [
    $this->html->_('Hello %n%, you last logged in %t% ago', [
        '%n%' => $client->getNickname(),
        '%t%' => $this->html->timeSince($client->getLoginDate())
    ])
]);

echo $this->html->link('account/logout', 'Logout')->setIcon('arrow-left');
