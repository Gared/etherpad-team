<?php
echo $this->Form->create('Group', array('controller' => 'groups', 'action' => 'search'));

echo $this->Form->input('searchText', array('label' => __('Search text')));
echo $this->Form->hidden('mappingId', array('value' => $mappingId));

echo $this->Form->end(__('Search Text'));
?>

<table>
    <?php foreach ($resultPads as $pad): ?>
        <tr>
            <td>
                <?php echo $this->Html->link($pad['padName'],
                    array('controller' => 'users', 'action' => 'connectToPad', $pad['padId'])); ?>
            </td>
            <td>
                <?php echo $this->Html->link("Show",
                    array('controller' => 'pads', 'action' => 'view', $pad['padId'])); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php 
                    echo $pad['resultText'];
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>