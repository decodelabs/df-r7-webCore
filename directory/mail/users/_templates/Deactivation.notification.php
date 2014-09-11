<?php $this->view->setSubject($this->_('%n% has deactivated their account', ['%n%' => $this['client']['fullName']])); ?>
<b><?php echo $this->esc($this['client']['fullName']); ?> has decided to deactivate their account.</b>
<?php if($reason = $this['deactivation']['reason']) { ?>

Reason: <i><?php echo $reason; ?></i>
<?php } ?>
<?php if($message = $this['deactivation']['comments']) { ?>

-----------------------------

<?php echo $message; ?> 

-----------------------------
<?php } ?>