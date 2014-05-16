<h1>
    <?php echo $this->Html->link($groupName,
                    array('controller' => 'groups', 'action' => 'view', $mappingId)); ?>
    - <b><?php echo $this->Html->link($padName,
                    array('controller' => 'users', 'action' => 'connectToPad', $padId)); ?></b>
    (Revision: <?php echo $padRev; ?>, <?php echo $padLastEdited; ?>)
    <?php if ($padPublicStatus) {
            echo $this->Html->link("Make private",
                    array('controller' => 'pads', 'action' => 'publicStatus', $padId, 0));
        } else { 
            echo $this->Html->link("Make public",
                    array('controller' => 'pads', 'action' => 'publicStatus', $padId, 1));
        }
    ?>
</h1>
<br/>
<div>
<?php echo __('Users').': '.$padUserCount; ?>
</div>
<br/>
<div>
    <fieldset>
        <legend>Text</legend>
        <?php echo $padText; ?>
    </fieldset>
</div>