<div>
	<?php
	echo $this->Form->create('GroupUsers', array('action' => 'acceptInvitation/'.$mappingId));
    echo $this->Form->end(__("Accept invitation"));
	?>
</div>