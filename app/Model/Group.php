<?php
App::uses('AppModel', 'Model');
/**
 * Group Model
 *
 * @property GroupUser $GroupUser
 */
class Group extends AppModel {

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'id';
    
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

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
		'name' => array(
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
    public function getGroupsOfUser($userId) {
        $connectedGroups = $this->find('all', array(
            'joins' => array( 
                    array( 
                        'table' => 'group_users',
                        'alias' => 'GroupUser',  
                        'conditions'=> array('GroupUser.user_id' => $userId,
                            'Group.id = GroupUser.group_id') 
                    )),
            'fields' => array('*')
        ));
        return $connectedGroups;
    }
    
    public function getGroupName($mappingId) {
        $result = $this->find('first', array(
            'conditions' => array('mapping_id' => $mappingId)
        ));
        if ($result) {
            return $result['Group']['name'];
        }
        return null;
    }
    
    public function getGroupId($mappingId) {
        $result = $this->find('first', array(
            'conditions' => array('mapping_id' => $mappingId)
        ));
        if ($result) {
            return $result['Group']['id'];
        }
        return null;
    }
    
    public function isUserRequestingAccessToGroup($userId, $mappingId) {
        return $this->checkPermission($userId, $mappingId, array(0));
    }
    
    public function isUserInvitedToGroup($userId, $mappingId) {
        return $this->checkPermission($userId, $mappingId, array(1));
    }
    
    public function isUserMemberOfGroup($userId, $mappingId) {
        return $this->checkPermission($userId, $mappingId, array(2,3));
    }
    
    public function isUserAdminOfGroup($userId, $mappingId) {
        return $this->checkPermission($userId, $mappingId, array(3));
    }

    /**
     * Check if user has permission for specified group
     * @param int $userId
     * @param string $mappingId
     * @param array $permission One of the specified permission. Example: array(2,3)
     * @return boolean whether user has permission
     */
    private function checkPermission($userId, $mappingId, $permission) {
        $ret = false;
        $groupCount = $this->find('count', array(
            'joins' => array(
                    array(
                        'table' => 'group_users',
                        'alias' => 'GroupUser',
                        'conditions'=> array('GroupUser.user_id' => $userId,
                            'Group.id = GroupUser.group_id',
                            'Group.mapping_id' => $mappingId,
                            'GroupUser.permission IN ('.implode(",", $permission).')') 
                    ))
        ));
        if ($groupCount > 0) {
            $ret = true;
        }
        return $ret;
    }
    
    public function deleteGroup($mappingId) {
        $result = $this->find('first', array(
            'conditions' => array('mapping_id' => $mappingId)
        ));
        $groupId = $result['Group']['id'];
        if ($groupId) {
            $db = ConnectionManager::getDataSource('default');
            if ($db->rawQuery("DELETE FROM group_users WHERE group_id = ".$groupId.";")) {
                if ($this->deleteAll(array('Group.mapping_id' => $mappingId))) {
                    return true;
                }
            }
        }
        return false;
    }
}
