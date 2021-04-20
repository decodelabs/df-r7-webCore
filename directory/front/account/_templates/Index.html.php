<?php
use DecodeLabs\Disciple;
use DecodeLabs\Tagged as Html;

echo Html::{'p'}([
    $this->html->_('Hello %n%, you last logged in %t%', [
        '%n%' => Disciple::getNickName(),
        '%t%' => Html::$time->since(Disciple::getLastLoginDate())
    ])
]);

echo Html::{'p'}($this->html->link('account/avatar', 'Update avatar')->setIcon('image'));
echo Html::{'p'}($this->html->link('account/change-password', 'Change password')->setIcon('edit'));
echo Html::{'p'}($this->html->link('account/logout', 'Logout')->setIcon('arrow-left'));
echo Html::{'p'}($this->html->link('account/deactivate', 'Close my account')->setIcon('remove'));
