<?php
if ($user->isNew()) {
    echo $generator->h4([
        'You have a new account on ',
        $this->app->getName()
    ]);
} else {
    echo $generator->h4([
        'Your email address has recently changed on ',
        $this->app->getName()
    ]);
}

echo $generator->p([
    'To make sure your details are correct and we can contact you, ',
    Html::br(),
    $generator->link(
        $this->uri('account/email-verify?key='.$key.'&user='.$user['id']),
        'please verify this email address'
    ),
    '.'
]);
