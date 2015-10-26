<?php
$this->view->setTitle($this->_('Password reset'));
?>
<h4>A password reset link was recently requested on <?php echo $this->esc($this->application->getName()); ?>.</h4>
<?php
echo $this->html('p',
    $this->html->link('account/reset-password?user='.$key['#user'].'&key='.$key['key'], $this->_(
        'Please follow this link to update your password.'
    ))
);
?> 
<p>This link will expire in the next 48 hours or if you change your password through other means.</p>
<p>If you did not request this link, don't worry, your account will not be affected and you do not need to take any action.</p>