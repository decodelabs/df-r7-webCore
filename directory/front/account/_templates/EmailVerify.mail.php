<?php
if($user->isNew()) {
    echo $this->html('h4', [
        'You have a new account on ',
        $this->application->getName()
    ]);
} else {
    echo $this->html('h4', [
        'Your email address has recently changed on ',
        $this->application->getName()
    ]);
}

echo $this->html('p', [
    'To make sure your details are correct and we can contact you, ',
    $this->html('br'),
    $this->html->link(
            'account/email-verify?key='.$key.'&user='.$user['id'],
            'please verify this email address'
        )
        ->setTarget('_blank'),
    '.'
]);
