<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
    public $components = array(
        'Session',
        'Auth' => array(
            'loginRedirect' => array('controller' => 'groups', 'action' => 'index'),
            'logoutRedirect' => array('controller' => 'users', 'action' => 'login'),
            'authenticate' => array(
                'Form' => array(
                    'fields' => array('username' => 'email'),
                    'passwordHasher' => 'Blowfish',
                    'scope' => array(
                        'User.verified_email' => 1
                    )
                )
            )
        )
    );

    /*

     */
    private static $permMap = array(
        0 => 'Requested access',
        1 => 'Invited',
        2 => 'Member',
        3 => 'Admin'
    );

    public function beforeFilter() {
        Configure::write('Cache.disable', Configure::read('debug'));
        $eplite = new EtherpadLiteClient(Configure::read('Eplite.apikey'), Configure::read('Eplite.apiurl')."/api");
        $this->eplite = new EtherpadLiteCaching($eplite);
        if ($this->Session->check('Config.language')) {
            Configure::write('Config.language', $this->Session->read('Config.language'));
        } else {
            $browserLanguage = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            $this->Session->write('Config.language', $browserLanguage);
            if ($browserLanguage == "de") {
                Configure::write('Config.language', "deu");
            }
        }
        //debug(Configure::read('Config.language')."/".$this->Session->read('Config.language'));
    }
    
    public function isAuthorized($user) {
        // TODO: Remove
        // Admin can access every action
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }

        // Default deny
        return false;
    }
    
    public static function splitPadId($padId) {
        $parts = explode('$', $padId);
        $ret = new stdClass();
        $groupName = null;
        $padName = null;
        if (count($parts) == 2) {
            $ret->groupName = $parts[0];
            $ret->padName = $parts[1];
        }
        return $ret;
    }
    
    public static function mapPermissionId($permId) {
        __('Requested access');
        __('Invited');
        __('Member');
        __('Admin');
        return __(AppController::$permMap[$permId]);
    }
    
    public function setLayoutParameters($title, $group_name_for_layout = null, $group_id_for_layout = null) {
        if ($group_name_for_layout) {
            $this->set('title_for_layout', $group_name_for_layout." - ".$title);
            $this->set('title', $title);
            $this->set('group_name_for_layout', $group_name_for_layout);
            $this->set('group_link_for_layout', array('controller' => 'groups', 'action' => 'view', $group_id_for_layout));
        } else {
            $this->set('title_for_layout', $title);
            $this->set('title', $title);
            $this->set('group_name_for_layout', null);
            $this->set('group_link_for_layout', null);
        }
    }
}