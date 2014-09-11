<?php 
$this->view->setTitle($this->_('Please verify your email address'));

if($this['user']->isNew()) { ?>
<h4>You have a new account on <?php echo $this->esc($this->application->getName()); ?>!</h4>
<?php } else { ?>
<h4>Your email address has recently changed on <?php echo $this->esc($this->application->getName()); ?>.</h4>
<?php } ?>
<p>To make sure your details are correct and we can contact you, please click the link below to verify this email address..</p>
<?php 
echo $this->html->element('p',
    $this->html->link('account/email-verify?key='.$this['key'].'&user='.$this['user']['id'], $this->_(
        'Complete verification'
    ))->setTarget('_blank')
);
?>