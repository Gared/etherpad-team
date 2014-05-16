<table>
    <?php foreach ($groups as $group): ?>
    <tr>
        <td>
            <?php 
            if ($group['groupPerm'] == 3) {
                echo $this->Html->link($this->Html->image("delete.png", array("alt" => __("(Delete)"), "title" => __("Delete team"), "width" => "20px", "height" => "20px")),
                    array('controller' => 'groups', 'action' => 'delete', $group['mappingId']),
                    array('escape' => false),
                    __("Do you really want to delete this team?"));
            }
            ?>
        </td>
        <td>
            <?php echo $this->Html->link($group['groupName'],
                array('controller' => 'groups', 'action' => 'view', $group['mappingId'])); ?>
        </td>
        <td>
            <?php echo AppController::mapPermissionId($group['groupPerm']); ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </table>
<?php unset($groups); ?>
<br>
<?php
    echo $this->Form->create('Group', array('controller' => 'groups', 'action' => 'create'));

    echo $this->Form->input('groupName', array('label' => __('Group Name')));

    echo $this->Form->end(__('Create group'));
?>