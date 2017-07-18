<?php
echo $this->html('h4', [
    'A password reset link was recently requested on ',
    $this->app->getName()
]);

echo $this->html('p',
    $this->html->link('account/reset-password?user='.$key['#user'].'&key='.$key['key'], $this->_(
        'Please follow this link to update your password.'
    ))
);

echo $this->html('p', 'This link will expire in the next 48 hours or if you change your password through other means.');
echo $this->html('p', 'If you did not request this link, don\'t worry, your account will not be affected and you do not need to take any action.');
