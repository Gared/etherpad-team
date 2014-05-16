<?php
App::uses('AppModel', 'Model');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

/**
 * User Model
 *
 * @property UserGroup $UserGroup
 */
class User extends AppModel {

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'id';
    
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'password' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	/*public $hasMany = array(
		'GroupsUser'
	);*/
    public function getUsersOfGroup($mappingId) {
        $users = $this->find('all', array(
            'joins' => array( 
                    array( 
                        'table' => 'group_users',
                        'alias' => 'GroupUser',  
                        'conditions'=> array('User.id = GroupUser.user_id') 
                    ),
                    array(
                        'table' => 'groups',
                        'alias' => 'Group',  
                        'conditions'=> array('Group.mapping_id' => $mappingId,
                            'Group.id = GroupUser.group_id') 
                    )
                ),
            'fields' => array('User.id, User.name, User.email, GroupUser.permission')
        ));
        return $users;
    }
    
    public function isUserAllowedToSeeUserProfile($activeUserId, $watchingUserId) {
        $user = $this->find('first', array(
            'joins' => array(
                array(
                    'table' => 'group_users',
                    'alias' => 'GroupUser',
                    'conditions' => array('User.id = GroupUser.user_id', 'GroupUser.permission' => 3, 'User.id' => $activeUserId)
                ),
                array(
                    'table' => 'group_users',
                    'alias' => 'GroupUser2',
                    'conditions' => array('GroupUser.group_id = GroupUser2.group_id', 'GroupUser2.user_id' => $watchingUserId)
                )
            )
        ));
        return $user == null ? false : true;
    }
    
    public function beforeSave($options = array()) {
        if (!$this->id) {
            $this->data['User']['password'] = Security::hash($this->data['User']['password'], 'blowfish');
        }
        return true;
    }

}
