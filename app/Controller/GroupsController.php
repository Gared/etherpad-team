<?php

App::import('Controller', 'Pads');
class GroupsController extends AppController {
    public function index() {
        $this->setLayoutParameters(__('Groups'));
        $groupIds = $this->eplite->listAllGroups()->groupIDs;
        
        $connectedGroups = $this->Group->getGroupsOfUser($this->Auth->user('id'));
        $myGroups = array();
        foreach ($connectedGroups as $group) {
            if (in_array($group['Group']['mapping_id'], $groupIds)) {
                $myGroups[] = array('groupName' => $group['Group']['name'],
                                    'mappingId' => $group['Group']['mapping_id'],
                                    'groupPerm' => $group['GroupUser']['permission']);
            }
        }

        $this->set('groups', $myGroups);
    }
    
    public function view($mappingId = null) {
        $this->set('isAdmin', false);
        $this->set('padIds', array());
        $this->set('groupName', '');
        $this->set('mappingId', '');
        $this->set('sessions', array());
        // check if registered user is a member of this group
        if ($this->Group->isUserMemberOfGroup($this->Auth->user('id'), $mappingId)) {
            $pads = $this->getPadList($mappingId);
            $isAdmin = $this->Group->isUserAdminOfGroup($this->Auth->user('id'), $mappingId);
            // Has user admin privileges?
            if ($isAdmin) {
                // Set session infos of team
                $sessions = $this->eplite->listSessionsOfGroup($mappingId);
                $sessionInfos = array();
                if ($sessions) {
                    foreach($sessions as $session) {
                        $sessionInfos[] = array(
                                                'authorName' => $this->eplite->getAuthorName($session->authorID),
                                                'validUntil' => $session->validUntil
                                                );
                    }
                }
                $this->set('sessions', $sessionInfos);
            }
            $groupName = $this->Group->getGroupName($mappingId);
            $this->set('isAdmin', $isAdmin);
            $this->set('pads', $pads);
            $this->set('groupName', $groupName);
            $this->set('mappingId', $mappingId);
            $this->setLayoutParameters(__("Pads"), $groupName, $mappingId);
        } else {
            $this->Session->setFlash(__('You are not a member of this group.'));
            if ($this->Group->isUserInvitedToGroup($this->Auth->user('id'), $mappingId)) {
                return $this->redirect(array('controller' => 'groupUsers', 'action' => 'acceptInvitation', $mappingId));
            } else {
                return $this->redirect(array('controller' => 'groupUsers', 'action' => 'requestMembership', $mappingId));
            }
        }
    }
    
    /**
     * Get a list of all pads of the $mappingId.
     * This method will also check if the group exists on eplite server.
     * 
     * @param string $mappingId
     * @return array $padList
     * 				 array(
     * 					 array('padName' => '',
     * 					     'padId' => '',
     * 						 'padUserCount' => 0,
     * 						 'padText' => ''
     * 					 )
     * 				 )
     */
    private function getPadList($mappingId) {
        $groupIds = $this->eplite->listAllGroups()->groupIDs;
        $this->loadModel("PadCategory");
        // check if group really exists on eplite server
        if (in_array($mappingId, $groupIds)) {
            $padIds = $this->eplite->listPads($mappingId)->padIDs;
            $pads = array();
            $padCategories = $this->PadCategory->getCategoriesForPads($padIds);
            foreach ($padIds as $padId) {
                $myPadCategories = array();
                foreach ($padCategories as $padCategory) {
                    if ($padCategory['PadCategory']['pad_id'] == $padId) {
                        $myPadCategories[] = $padCategory['Category'];
                    }
                }
                $pads[] = array('padName' => parent::splitPadId($padId)->padName,
                                'padId' => $padId,
                                'padCategories' => $myPadCategories,
                                'padUserCount' => $this->eplite->padUsersCount($padId)->padUsersCount,
                                'padText' => $this->eplite->getText($padId)->text
                                );
            }
            return $pads;
        } else {
            $this->Session->setFlash(__('Group does not exist.'));
        }
        return array();
    }
    
    public function create() {
        if ($this->request->is('post')) {
            $requestData = $this->request->data['Group'];
            $groupName = $requestData['groupName'];
            if ($groupName != null && trim($groupName) != "") {
                $groupId = $this->eplite->createGroup()->groupID;
                
                $this->loadModel('GroupUser');
                
                $groupData = array(
                    'mapping_id' => $groupId,
                    'name' => $groupName
                );
                if ($this->Group->save($groupData)) {
                    $groupId = $this->Group->id;
                    debug($groupId);
                    $groupUserData = array(
                        'user_id' => $this->Auth->user('id'),
                        'group_id' => $groupId,
                        'permission' => 3
                    );
                    if ($this->GroupUser->save($groupUserData)) {
                        return $this->redirect(array('action' => 'index'));
                    } else {
                        debug($this->GroupUser->validationErrors);
                    }
                } else {
                    debug($this->Group->validationErrors);
                }
            }
        }
    }
    
    /**
     * Deletes a group
     * 
     * @param unknown_type $mappingId
     */
    public function delete($mappingId) {
        if ($this->Group->isUserAdminOfGroup($this->Auth->user('id'), $mappingId)) {
            if ($this->Group->deleteGroup($mappingId)) {
                debug($this->eplite->deleteGroup($mappingId));
            } else {
                $this->Session->setFlash(__('Could not delete group.'));
            }
        }
        //return $this->redirect(array('action' => 'index'));
    }
    
    public function search($mappingId = null) {
        $this->setLayoutParameters(__('Search'));
        if ($mappingId) {
            $this->set('mappingId', $mappingId);
        }
        $this->set('resultPads', array());
        if ($this->request->is('post') && key_exists('searchText', $this->request->data['Group'])) {
            $mappingId = $this->request->data['Group']['mappingId'];
            $searchText = $this->request->data['Group']['searchText'];
            $this->set('mappingId', $mappingId);
            $pads = $this->getPadList($mappingId);
            $padsController = new PadsController();
            $searchPads = array();
            foreach ($pads as $padObj) {
                $pad = parent::splitPadId($padObj['padId']);
                if ($pad->groupName != null && $pad->padName != null) {
                    $this->loadModel('Group');
                    if ($this->Group->isUserMemberOfGroup($this->Auth->user('id'), $pad->groupName)) {
                        $padText = $this->eplite->getText($padObj['padId'])->text;
                        $resultSnippets = $this->getResultSnippetsOfSearchText($padText, $searchText);
                        $padObj['resultText'] = implode(" - ", $resultSnippets);
                        $searchPads[] = $padObj;
                    }
                }
            }
            debug($searchPads);
            $this->set('resultPads', $searchPads);
        }
        $groupName = $this->Group->getGroupName($mappingId);
        $this->set('groupName', $groupName);
    }
    
    private function getResultSnippetsOfSearchText($padText, $searchText) {
        debug($padText);
        $size = 30;
        $searchPos = 0;
        $textSize = strlen($padText);
        $searchStrLength = strlen($searchText);
        $snippets = array();
        debug($textSize);
        $snippet = "";
        while (($searchPos = stripos($padText, $searchText, $searchPos))
                && $searchPos !== false
                && $searchPos < $textSize) {
            debug($searchPos);
            $snippet = '...'.substr($padText, ($searchPos >= $size ? $searchPos - $size : 0), $searchPos + $searchStrLength + $size).'...';
            $snippet = str_ireplace($searchText, '<font color="red"><strong>'.$searchText.'</strong></font>', $snippet);
            $snippets[] = $snippet;
            $searchPos = $searchPos + $searchStrLength + $size;
        }
        return $snippets;
    }
}
?>