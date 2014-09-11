<?php $this->view->setSubject($this->_('New invite request from %n%', ['%n%' => $this['request']['name']])); ?>
<b><?php echo $this->esc($this['request']['name']); ?>, <?php echo $this->esc($this['request']['companyPosition']); ?> of <?php echo $this->esc($this['request']['companyName']); ?> has asked to become a member at <?php echo $this->esc($this->context->application->getName()); ?></b>
<?php if($message = $this['request']['message']) { ?>

-----------------------------

<?php echo $message; ?> 

-----------------------------
<?php } ?>

Please follow this link to see more details and respond: <?php echo $this->html->basicLink('~admin/users/invite-requests/respond?request='.$this['request']['id'])->setAttribute('target', '_blank'); ?>