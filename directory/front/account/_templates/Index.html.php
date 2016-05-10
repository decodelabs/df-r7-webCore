<?php
$client = $this->context->user->client;

echo $this->html('p', [
    $this->html->_('Hello %n%, you last logged in %t% ago', [
        '%n%' => $client->getNickname(),
        '%t%' => $this->html->timeSince($client->getLoginDate())
    ])
]);

echo $this->html('p', $this->html->link('account/avatar', 'Update avatar')->setIcon('image'));
echo $this->html('p', $this->html->link('account/change-password', 'Change password')->setIcon('edit'));
echo $this->html('p', $this->html->link('account/logout', 'Logout')->setIcon('arrow-left'));
echo $this->html('p', $this->html->link('account/deactivate', 'Close my account')->setIcon('remove'));
