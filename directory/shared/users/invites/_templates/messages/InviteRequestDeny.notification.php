<?php $this->view->setSubject($this->_('Your invite request at %n%', ['%n%' => $this->context->application->getName()])); ?>
<b>Sorry, but your request to join <?php echo $this->esc($this->context->application->getName()); ?> has been denied.</b>
<?php if($message = $this['message']) { ?>

-----------------------------

<?php echo $message; ?> 

-----------------------------
<?php } ?>