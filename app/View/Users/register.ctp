<?php
echo $this->Form->create('User');
echo $this->Form->input('email', array('label' => __('E-Mail')));
echo $this->Form->input('password', array('label' => __('Password')));
echo $this->Form->end(__('Register'));
echo $this->Html->link(
    __('Login'),
    array('controller' => 'users', 'action' => 'login'));
?>