Hello,<br/>
<br/>
a user invited you to join his team.<br/>
Team: <?php echo $team_name; ?><br/>
<br/>
Click here to accept the invitation:<br/>
<?php echo $this->Html->link($invitation_url); ?><br/>
<br/>
<?php echo $site_name; ?><br/>
<?php echo $this->Html->link($site_url); ?>