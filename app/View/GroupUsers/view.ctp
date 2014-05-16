    <table>
    	<thead>
    		<tr>
    			<td>
    				
    			</td>
    			<td>
    				<?php echo __("Name"); ?>
    			</td>
    			<td>
    				<?php echo __("E-Mail"); ?>
    			</td>
    			<td>
    				<?php echo __("Status"); ?>
    			</td>
    			<td>
    				<?php echo __("Admin"); ?>
    			</td>
    		</tr>
    	</thead>
    	<tbody>
	        <?php foreach ($users as $user): ?>
	        <tr>
	            <td>
	                <?php echo $this->Html->link($this->Html->image("deny.png", array("alt" => __("(Remove)"), "title" => __("(Remove)"), "width" => "20px", "height" => "20px")),
	                    array('controller' => 'groupUsers', 'action' => 'remove', $mappingId, $user['User']['id']),
	                    array('escape' => false),
	                    __("Do you really want to remove this user from this team?")); ?>
	            </td>
	            <td>
	                <?php echo $this->Html->link($user['User']['name'],
	                    array('controller' => 'users', 'action' => 'view', $user['User']['id'])); ?>
	            </td>
	            <td>
	                <?php echo $user['User']['email']; ?>
	            </td>
	            <td>
	                (<?php 
	                    echo AppController::mapPermissionId($user['GroupUser']['permission']);
	                    if ($user['GroupUser']['permission'] == 0) {
	                        echo $this->Html->link($this->Html->image("accept.png", array("alt" => "(Accept)", "width" => "20px", "height" => "20px")),
	                            array('action' => 'acceptRequest', $mappingId, $user['User']['id']),
	                            array('escape' => false))." / ";
	                        echo $this->Html->link($this->Html->image("deny.png", array("alt" => "(Deny)", "width" => "20px", "height" => "20px")),
	                            array('action' => 'denyRequest', $mappingId, $user['User']['id']),
	                            array('escape' => false));
	                    }
	                ?>)
	            </td>
	            <td>
	           		<?php
	           		if ($user['GroupUser']['permission'] == 2 || $user['GroupUser']['permission'] == 3) {
	            		echo $this->Html->link($user['GroupUser']['permission'] == 2 ? __("Make admin") : __("Make member"),
	                            array('action' => 'toggleAdminStatus', $mappingId, $user['User']['id']));
	                }
	                ?>
	            </td>
	        </tr>
	        <?php endforeach; ?>
		</tbody>        
    </table>
    <?php unset($users); ?>
    <br>
    <?php
        echo $this->Form->create('GroupUser', array('action' => 'invite'));

        echo $this->Form->input('email', array('label' => __('Email')));
        echo $this->Form->hidden('mappingId', array('value' => $mappingId));

        echo $this->Form->end(__('Invite user'));
    ?>