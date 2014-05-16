<div>
	<?php
	echo $this->Form->create('GroupUsers', array('action' => 'requestMembership/'.$mappingId));
    echo $this->Form->end($alreadyRequested == true ? __("Membership already requested") : __("Request membership"));
	?>
</div>