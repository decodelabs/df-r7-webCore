<?php $this->view->setSubject($this->_('Come and join us at %n%', ['%n%' => $this->context->application->getName()])); ?>
<b><?php echo $this['invite']['owner']->getFullName(); ?> has invited you to become a member at <?php echo $this->esc($this->context->application->getName()); ?></b>
<?php if($message = $this['invite']['message']) { ?>

-----------------------------

<?php echo $message; ?> 

-----------------------------
<?php } ?>

Please follow this link to set up your account: <?php echo $this->html->basicLink('account/register?invite='.$this['invite']['key'])->setAttribute('target', '_blank'); ?>