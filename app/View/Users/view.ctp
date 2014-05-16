<div>
    <div>
        <?php
		if ($isLoggedInUser === true) {
			echo $this->Form->create('User', array('controller' => 'user', 'action' => 'changeUserSettings'));
			echo $this->Form->input('name', array('value' => $name));
		} else {
			echo "Name: ".$name; 
		}
		?>
    </div>
    <div>
        Email: <?php echo $email; ?>
    </div>
    <?php if ($isLoggedInUser === true) { ?>
    <div>
    	<?php
    		echo $this->Form->input('pw', array('label' => __("Password"), 'type' => 'password'));
    		echo $this->Form->input('repeatpw', array('label' => __("Repeat password"), 'type' => 'password'));
    	?>
    </div>
    <?php } ?>
    <br/>
    <table>
    	<thead>
    		<td>
    			<?php echo __("Team"); ?>
    		</td>
    		<td>
    			<?php echo __("Status"); ?>
    		</td>
    		<td>
    			<?php echo __("Sessions"); ?>
    		</td>
    	</thead>
    	<tbody>
        <?php foreach ($groups as $group): ?>
	        <tr>
	            <td>
	                <?php echo $this->Html->link($group['Group']['name'],
	                    array('controller' => 'groups', 'action' => 'view', $group['Group']['mapping_id'])); ?>
	            </td>
	            <td>
	                <?php
	                    echo AppController::mapPermissionId($group['GroupUser']['permission']);
	                ?>
	            </td>
	            <td>
	                <?php foreach ($group['Sessions'] as $session): ?>
	                    <div>
	                        <?php echo date('d.m.Y H:i:s', $session['validUntil']); ?>
	                    </div>
	                <?php endforeach; ?>
	            </td>
	        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php
    	echo $this->Form->end(__('Save'));
    	if ($isLoggedInUser) {
		    echo $this->Html->link(__("Delete account"),
			    array('controller' => 'users', 'action' => 'delete'),
				null,
			    __("Do you really want to delete your account?"));
		} 
	?>
</div>