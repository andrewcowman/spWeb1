<!--
* 1 = everybody
* 2 = friends
-->
<?php if($user['show'] == 1) { ?>
<?= $this->element('/Users/view_all'); ?>
<?php } else { ?>
<?= $this->element('/Users/view_private'); ?>
<?php } ?>
