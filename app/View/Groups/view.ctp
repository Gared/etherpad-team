<div style="float:right;">
    <table>
        <tr>
            <td></td>
            <td>
                <?php echo __("Author"); ?>
            </td>
            <td>
                <?php echo __("Valid until"); ?>
            </td>
        </tr>
        <?php foreach ($sessions as $session): ?>
            <tr>
                <td>
                    <?php
                        // TODO: Remove function to delete sessions
                        /*echo $this->Html->link($this->Html->image("delete.png", array("alt" => "(Delete)", "title" => __("Delete pad"), "width" => "20px", "height" => "20px")),
                            array('controller' => 'pads', 'action' => 'delete', 1),
                            array('escape' => false),
                            __("Do you really want to delete this session?"));*/
                    ?>
                </td>
                <td>
                    <?php echo $session['authorName']; ?>
                </td>
                <td>
                    <?php echo date('d.m.Y H:i:s', $session['validUntil']); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php
if ($isAdmin) {
    echo $this->Html->link($this->Html->image("manage_users.png", array("alt" => __("Manage users"), "title" => __("Manage users"), "width" => "32px", "height" => "32px")),
                    array('controller' => 'groupUsers', 'action' => 'view', $mappingId),
                    array('escape' => false)); 
}
echo $this->Html->link($this->Html->image("manage_categories.png", array("alt" => __("Manage categories"), "title" => __("Manage categories"), "width" => "32px", "height" => "32px")),
                    array('controller' => 'categories', 'action' => 'manage', $mappingId),
                    array('escape' => false));

echo $this->Html->link($this->Html->image("search.png", array("alt" => __("Pad search"), "title" => __("Pad search"), "width" => "32px", "height" => "32px")),
                    array('controller' => 'groups', 'action' => 'search', $mappingId),
                    array('escape' => false));
?>
<br/>
<br/>
    <table>
    	<thead>
    		<tr>
    			<td>
    			</td>
    			<td>
    				<?php echo __("Name"); ?>
    			</td>
    			<td>
    				<?php echo __("Categories"); ?>
    			</td>
    			<td>
    				<?php echo __("Online Users"); ?>
    			</td>
    			<td>
    				<?php echo __("Action"); ?>
    			</td>
    		</tr>
    	</thead>
    	<tbody>
	        <?php foreach ($pads as $pad): ?>
	        <tr>
	            <td>
	                <?php if ($isAdmin) {
	                    echo $this->Html->link($this->Html->image("delete.png", array("alt" => "(Delete)", "title" => __("Delete pad"), "width" => "20px", "height" => "20px")),
		                    array('controller' => 'pads', 'action' => 'delete', $pad['padId']),
		                    array('escape' => false),
		                    __("Do you really want to delete this pad?"));
		                    } ?>
	            </td>
	            <td>
	                <?php echo $this->Html->link($pad['padName'],
	                    array('controller' => 'users', 'action' => 'connectToPad', $pad['padId'])); ?>
	            </td>
	            <td>
	            	<?php foreach ($pad['padCategories'] as $padCategory): ?>
	            		<span id="<?php echo $padCategory['id']; ?>" class="category" style="background-color:#<?php echo $padCategory['color']; ?>;">
			                <?php echo $padCategory['name']; ?>
			            </span>
	            	<?php endforeach; ?>
	            </td>
	            <td>
	                <?php echo "(".$pad['padUserCount'].")"; ?>
	            </td>
	            <td>
	                <?php echo $this->Html->link(__("Show"),
	                    array('controller' => 'pads', 'action' => 'view', $pad['padId'])); ?>
	            </td>
	        </tr>
	        <?php endforeach; ?>
	    </tbody>
    </table>
    <?php unset($pads); ?>
    <br>
    <?php
        echo $this->Form->create('Pad', array('controller' => 'pads', 'action' => 'create', $mappingId));

        echo $this->Form->input('padName', array('label' => __('Pad name')));
        echo $this->Form->hidden('mappingId', array('value' => $mappingId));

        echo $this->Form->end(__('Create pad'));
    ?>