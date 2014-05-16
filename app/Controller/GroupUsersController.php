<?php

App::import('Lib', 'Emails');

class GroupUsersController extends AppController {
    
    public function isAuthorized($groupName) {
        if ($this->Group->isUserAdminOfGroup($this->Auth->user('id'), $groupName)) {
            return true;
        }
        return false;
    }
    
    public function view($mappingId) {
        $this->loadModel('Group');
        if ($this->Group->isUserAdminOfGroup($this->Auth->user('id'), $mappingId)) {
            $this->loadModel('User');
            $users = $this->User->getUsersOfGroup($mappingId);
            $groupName = $this->Group->getGroupName($mappingId);
            
            $this->set('users', $users);
            $this->set('mappingId', $mappingId);
            $this->set('groupName', $groupName);
            
            $this->setLayoutParameters(__('User management'), $groupName, $mappingId);
        }
    }
    
    public function acceptRequest($mappingId, $userId) {
        $this->loadModel('Group');
        if ($this->Group->isUserAdminOfGroup($this->Auth->user('id'), $mappingId) 
            && $this->Group->isUserRequestingAccessToGroup($userId, $mappingId)) {
            $this->setPermission($mappingId, $userId, 2);
        }
        return $this->redirect(array('action' => 'view', $mappingId));
    }
    
    public function denyRequest($mappingId, $userId) {
        $this->loadModel('Group');
        if ($this->Group->isUserAdminOfGroup($this->Auth->user('id'), $mappingId)) {
            if ($this->Group->isUserRequestingAccessToGroup($userId, $mappingId)) {
                $this->setPermission($mappingId, $userId, -1);
            }
        }
        return $this->redirect(array('action' => 'view', $mappingId));
    }
    
    public function toggleAdminStatus($mappingId, $userId) {
        $this->loadModel('Group');
        if ($this->Group->isUserAdminOfGroup($this->Auth->user('id'), $mappingId)) {
            if ($this->Group->isUserAdminOfGroup($userId, $mappingId)) {
                $this->setPermission($mappingId, $userId, 2);
            } else if ($this->Group->isUserMemberOfGroup($userId, $mappingId)) {
                $this->setPermission($mappingId, $userId, 3);
            }
        }
        return $this->redirect(array('action' => 'view', $mappingId));
    }
    
    /**
     * Set a permission for a user for the specified group
     * 
     * @param unknown_type $mappingId
     * @param unknown_type $userId
     * @param integer $permission 
     * 0: User requested to access group
     * 1: Invitation of the group
     * 2: Member
     * 3: Admin
     */
    private function setPermission($mappingId, $userId, $permission) {
        $this->loadModel('GroupUser');
        $groupUser = $this->GroupUser->find('first', array(
            'joins' => array(
                array(
                    'table' => 'groups',
                    'alias' => 'Group',
                    'conditions' => array('Group.id = GroupUser.group_id')
                )
            ),
            'conditions' => array(
                'GroupUser.user_id' => $userId,
                'Group.mapping_id' => $mappingId
            )
        ));
        if ($groupUser != null) {
            if ($permission >= 0) {
                $this->GroupUser->updateAll(array('permission' => $permission),
                    array('user_id' => $groupUser['GroupUser']['user_id'],
                        'group_id' => $groupUser['GroupUser']['group_id'])
                );
            } else if ($permission == -1) {
                $db = ConnectionManager::getDataSource('default');
                $db->rawQuery("DELETE FROM group_users WHERE user_id=".
                    $groupUser['GroupUser']['user_id']." AND group_id = ".
                    $groupUser['GroupUser']['group_id'].";");
            }
        } else {
            $groupId = $this->Group->getGroupId($mappingId);
            $this->loadModel('GroupUser');
            $db = ConnectionManager::getDataSource('default');
            $db->rawQuery("INSERT INTO group_users VALUES (
                    ".$userId.",
                    ".$groupId.",
                    ".$permission."
                )");
        }
    }
    
    /**
     * Removes a user from a group
     * 
     * @param string $mappingId
     * @param int $userId
     */
    public function remove($mappingId, $userId) {
        $this->loadModel('Group');
        if ($this->Group->isUserAdminOfGroup($this->Auth->user('id'), $mappingId)) {
            $this->setPermission($mappingId, $userId, -1);
        }
        return $this->redirect(array('action' => 'view', $mappingId));
    }
    
    /**
     * Invites a user (email) to a group
     */
    public function invite() {
        $this->loadModel('Group');
        $requestData = $this->request->data['GroupUser'];
        $mappingId = $requestData['mappingId'];
        if ($this->Group->isUserAdminOfGroup($this->Auth->user('id'), $mappingId)) {
            if ($this->request->is('post')) {
                if ($email = $requestData['email']) {
                    $this->loadModel('User');
                    // If user already registered
                    if ($user = $this->User->findByEmail($email)) {
                        $group = $this->Group->findByMappingId($mappingId);
                        $groupId = $group['Group']['id'];
                        // User not member of this group yet
                        $group_user = $this->GroupUser->findByUser_idAndGroup_id($user['User']['id'], $groupId);
                        if (!$group_user) {
                            $this->loadModel('GroupUser');
                            $db = ConnectionManager::getDataSource('default');
                            $db->rawQuery("INSERT INTO group_users VALUES (
                                    ".$user['User']['id'].",
                                    ".$groupId.",
                                    1
                                )");
                            //TODO: make session flash work
                            $this->Session->setFlash(__('User invited'+': '+$email));
                            
                            $invitationEmail = Emails::getInvitationMail($group['Group']['name'],
                                    $this->redirect(array('controller' => 'groupUsers', 'action' => 'acceptInvitation', $mappingId)),
                                    $email);
                            if ($invitationEmail->send()) {
                                $this->Session->setFlash(__('Invitation sent'));
                            } else {
                                $this->Session->setFlash(__('Error on sending invitation email!'));
                            }
                        }
                        
                    }
                }
            }
        }
        return $this->redirect(array('action' => 'view', $mappingId));
    }
    
    public function requestMembership($mappingId) {
        $this->loadModel('Group');
        if ($this->Group->isUserMemberOfGroup($this->Auth->user('id'), $mappingId)) {
            return $this->redirect(array('controller' => 'groups', 'action' => 'view', $mappingId));
        }
        if ($this->request->is('post')) {
            $this->setPermission($mappingId, $this->Auth->user('id'), 0);
        }
        $groupName = $this->Group->getGroupName($mappingId);
        
        $this->setLayoutParameters(__('Request membership to group'), $groupName, $mappingId);
        $this->set('mappingId', $mappingId);
        $this->set('groupName', $groupName);
        $this->set('alreadyRequested', $this->Group->isUserRequestingAccessToGroup($this->Auth->user('id'), $mappingId));
    }
    
    public function acceptInvitation($mappingId) {
        $this->loadModel('Group');
        if ($this->Group->isUserMemberOfGroup($this->Auth->user('id'), $mappingId)) {
            return $this->redirect(array('controller' => 'groups', 'action' => 'view', $mappingId));
        }
        if ($this->request->is('post') && $this->Group->isUserInvitedToGroup($this->Auth->user('id'), $mappingId)) {
            // Make user member of this group
            $this->setPermission($mappingId, $this->Auth->user('id'), 2);
            return $this->redirect(array('controller' => 'groups', 'action' => 'view', $mappingId));
        }
        $groupName = $this->Group->getGroupName($mappingId);
        
        $this->setLayoutParameters(__('Accept invitation to group'), $groupName, $mappingId);
        $this->set('mappingId', $mappingId);
        $this->set('groupName', $groupName);
    }
}
?>