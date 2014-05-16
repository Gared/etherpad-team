<?php

//App::uses('EtherpadLiteClient', 'Lib');
class PadsController extends AppController {
    public $helpers = array('Html', 'Form');
    public $components = array('Session');
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('show');
    }
    
    public function isAuthorized($user) {
        //TODO: Remove
        // Admin can access every action
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }

        // Default deny
        return true;
    }
    
    public function index() {
    // TODO
        debug($this->eplite->listAllGroups());
        $this->setLayoutParameters("Pads");
    }
    
    public function create() {
        if ($this->request->is('post')) {
            $requestData = $this->request->data['Pad'];
            debug($requestData);
            $mappingId = $requestData['mappingId'];
            $padName = $requestData['padName'];
            
            if ($mappingId != null) {
                //TODO: Set arguments
                $padId = $this->eplite->createGroupPad($mappingId, $padName, null)->padID;
                if ($padId != null) {
                    return $this->redirect(array('controller' => 'pads', 'action' => 'view', $padId));
                }
            }
        }
    }
    
    public function view($padId) {
        $pad = parent::splitPadId($padId);
        if ($pad->groupName != null && $pad->padName != null) {
            $mappingId = $pad->groupName;
            $this->loadModel('Group');
            if ($this->Group->isUserMemberOfGroup($this->Auth->user('id'), $mappingId)) {
                $padText = $this->eplite->getHTML($padId)->html;
                $padRev = $this->eplite->getRevisionsCount($padId)->revisions;
                $lastEdited = $this->eplite->getLastEdited($padId)->lastEdited;
                $padLastEdited = (date('d.m.Y H:i:s', $lastEdited/1000));
                $padUserCount = $this->eplite->padUsersCount($padId)->padUsersCount;
                $padPublicStatus = $this->eplite->getPublicStatus($padId)->publicStatus;
                $groupName = $this->Group->getGroupName($mappingId);
                
                $this->set('groupName', $groupName);
                $this->set('mappingId', $mappingId);
                $this->set('padId', $padId);
                $this->set('padName', $pad->padName);
                $this->set('padText', $padText);
                $this->set('padRev', $padRev);
                $this->set('padLastEdited', $padLastEdited);
                $this->set('padUserCount', $padUserCount);
                $this->set('padPublicStatus', $padPublicStatus);
                $this->setLayoutParameters('Pad '.$pad->padName);
            }
        }
    }
    
    public function show($padId) {
        $pad = parent::splitPadId($padId);
        $url = Configure::read('Eplite.frameurl')."/p/".$padId;
        
        $this->setLayoutParameters('Pad '.$pad->padName);
        $this->set('url', $url);
    }
    
    public function delete($padId) {
        $pad = parent::splitPadId($padId);
        if ($pad->groupName != null && $pad->padName != null) {
            $this->loadModel('Group');
            if ($this->Group->isUserAdminOfGroup($this->Auth->user('id'), $pad->groupName)) {
                $this->eplite->deletePad($padId);
                
            }
        }
        return $this->redirect(array('controller' => 'groups', 'action' => 'view', $pad->groupName));
    }
    
    public function publicStatus($padId, $public) {
        $pad = parent::splitPadId($padId);
        if ($pad->groupName != null && $pad->padName != null) {
            $this->loadModel('Group');
            if ($this->Group->isUserMemberOfGroup($this->Auth->user('id'), $pad->groupName)) {
                $status = $public ? "true" : "false";
                $this->eplite->setPublicStatus($padId, $status);
            }
        }
        return $this->redirect(array('action' => 'view', $padId));
    }
}

?>