<p>Hello <?php echo $this->esc($this['client']->getNickname()); ?>, you last logged in <?php echo $this->html->timeSince($this['client']->getLoginDate()); ?> ago.</p>
<?php echo $this->html->link('account/logout', 'Logout')->setIcon('arrow-left'); ?>
