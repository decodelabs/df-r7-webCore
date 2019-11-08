<?php
echo $generator->h4([
    'A password reset link was recently requested on ',
    $this->app->getName()
]);

echo $generator->p($generator->link(
    $this->uri('account/reset-password?user='.$key['#user'].'&key='.$key['key']),
    'Please follow this link to update your password.'
));

echo $generator->p('This link will expire in the next 48 hours or if you change your password through other means.');
echo $generator->p('If you did not request this link, don\'t worry, your account will not be affected and you do not need to take any action.');
