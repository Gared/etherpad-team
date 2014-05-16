<?php
echo $this->Form->create('User');
echo $this->Form->input(
    'email',
    array('label' => __('email'))
);
echo $this->Form->input(
    'password',
    array('label' => __('password'))
);
echo $this->Form->end(__('Login'));
echo $this->Html->link(
    __('Register'),
    array('controller' => 'users', 'action' => 'register'));
?>