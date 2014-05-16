<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo Configure::read('Eplite.sitename') ." - ". $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('style');
        echo $this->Html->script('jquery-2.0.3.min');
        echo $this->Html->script('jquery-ui-1.10.4.custom.min');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>
	<div id="container">
		<div id="header">
	        <div id="header_bar">
	        	<div id="site_icon">
	        		<?php echo $this->Html->link(
	        					$this->Html->image("ep_team.png", 
	        							array("alt" => __("Start page"), 
	        								"title" => __("Start page"), 
	        								"width" => "40px", 
	        								"height" => "40px"
	        							)
	        					),
	        					array('controller' => 'groups', 'action' => 'index'),
                    			array('escape' => false)); ?>
	        	</div>
	            <ul>
	                <li>
	                    <?php echo $this->Html->link(__('Groups'),
	                        array('controller' => 'groups', 'action' => 'index')); ?>
	                </li>
	                <li>
	                    <?php echo $this->Html->link(__('Pads'),
	                        array('controller' => 'pads', 'action' => 'index')); ?>
	                </li>
	                <li>
	                    <?php echo $this->Html->link(__('Profile'),
	                        array('controller' => 'users', 'action' => 'view')); ?>
	                </li>
	                <?php if (AuthComponent::user('id')) { ?>
	                <li>
	                    <?php echo $this->Html->link(__('Logout'),
	                        array('controller' => 'users', 'action' => 'logout')); ?>
	                </li>
	                <?php } else { ?>
	                <li>
	                    <?php echo $this->Html->link(__('Login'),
	                        array('controller' => 'users', 'action' => 'login')); ?>
	                </li>
	                <?php } ?>
	            </ul>
	        </div>
        </div>
		<div id="content">
			<h1>
				<?php echo Configure::read('Eplite.sitename'); ?>
				<?php if (isset($group_name_for_layout)) {
					echo " - ". $this->Html->link($group_name_for_layout, $group_link_for_layout);
				} ?>
				<?php echo " - ". $title; ?>
			</h1>
			
			<?php echo $this->Session->flash(); ?>

			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">
			Powered by: <a href="https://github.com/Gared/etherpad-team" target="_blank">Etherpad-Team</a>
		</div>
	</div>
	<?php //echo $this->element('sql_dump'); ?>
</body>
</html>
