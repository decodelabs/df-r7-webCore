<?php
use DecodeLabs\Tagged as Html;

$client = $this->context->user->client;

echo Html::{'p'}([
    $this->html->_('Hello %n%, you last logged in %t% ago', [
        '%n%' => $client->getNickname(),
        '%t%' => Html::$time->since($client->getLoginDate())
    ])
]);

echo Html::{'p'}($this->html->link('account/avatar', 'Update avatar')->setIcon('image'));
echo Html::{'p'}($this->html->link('account/change-password', 'Change password')->setIcon('edit'));
echo Html::{'p'}($this->html->link('account/logout', 'Logout')->setIcon('arrow-left'));
echo Html::{'p'}($this->html->link('account/deactivate', 'Close my account')->setIcon('remove'));
