<?php

App::uses('CakeEmail', 'Network/Email');
App::import('Lib', 'Emails');

class UsersController extends AppController {
    public $helpers = array('Html', 'Form');
    public $components = array('Session', 'Cookie');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('login', 'register', 'activate');
    }
    
    public function index() {
        $this->redirect(array('action' => 'login'));
    }
    
    public function login() {
        $this->setLayoutParameters(__('Login'));
        if ($this->Auth->loggedIn()) {
            return $this->redirect(array('controller' => 'groups', 'action' => 'index'));
        }
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                $this->deleteSession($this->Auth->user('id'));
                return $this->redirect($this->Auth->redirectUrl());
            }
            $this->Session->setFlash(__('Invalid email or password, try again'));
        }
    }
    
    public function logout() {
        $this->deleteSession($this->Auth->user('id'));
        return $this->redirect($this->Auth->logout());
    }
    
    public function view($userId = null) {
        $this->setLayoutParameters(__('Profile'));
        $user = null;
        if ($userId == null) {
            $user = $this->User->findById($this->Auth->user('id'));
        } else {
            if ($this->User->isUserAllowedToSeeUserProfile($this->Auth->user('id'), $userId)) {
                $user = $this->User->findById($userId);
            } else {
                $this->Session->setFlash(__('You are not allowed to view this user profile.'));
                return $this->redirect($this->Auth->loginAction);
            }
        }
        if ($user != null) {
            $isLoggedInUser = false;
            if ($user['User']['id'] == $this->Auth->user('id')) {
                $isLoggedInUser = true;
            }
            $this->set('name', $user['User']['name']);
            $this->set('isLoggedInUser', $isLoggedInUser);
            $this->set('email', $user['User']['email']);
            $authorId = $this->eplite->createAuthorIfNotExistsFor($user['User']['id'], $user['User']['name'])->authorID;
            $sessions = $this->eplite->listSessionsOfAuthor($authorId);
            $this->loadModel('Group');
            $groups = $this->Group->getGroupsOfUser($user['User']['id']);
            
            for ($i=0;$i<count($groups);$i++) {
                $groups[$i]['Sessions'] = array();
                if ($sessions) {
                    foreach ($sessions as $session) {
                        if ($groups[$i]['Group']['mapping_id'] == $session->groupID) {
                            $groups[$i]['Sessions'][] = array('validUntil' => $session->validUntil);
                        }
                    }
                }
            }
            $this->set('groups', $groups);
        }
    }
    
    public function changeUserSettings() {
        if ($this->request->is('post')) {
            $user = $this->User->findById($this->Auth->user('id'));
            if ($user != null) {
                $name = $this->request->data['User']['name'];
                $user['User']['name'] = $name;
                
                $pw = $this->request->data['User']['pw'];
                $repeatpw = $this->request->data['User']['repeatpw'];
                if ($pw && $pw != "" && $repeatpw && $repeatpw != "") {
                    if ($pw == $repeatpw) {
                        $user['User']['password'] = Security::hash($pw, 'blowfish');;
                        if ($this->User->save($user)) {
                            $this->Session->setFlash("Successfully saved");
                        }
                    } else {
                        $this->Session->setFlash("Passwords doesn't match");
                    }
                } else {
                    if ($this->User->save($user)) {
                        $this->Session->setFlash("Successfully saved");
                    }
                }
            }
        }
        return $this->redirect(array('action' => 'view'));
    }
    
    public function register() {
        $this->setLayoutParameters(__('Register'));
		if ($this->request->is('post')) {
            debug($this->request->data);
            $userEmail = $this->request->data['User']['email'];
            debug($this->User->findByEmail($userEmail));
            if (count($this->User->findByEmail($userEmail)) === 0) {
                $this->User->create();
                if ($this->User->save($this->request->data)) {
                    $hash=sha1(time().rand(0,10000));
                    $url = Router::url(array('controller'=>'users','action'=>'activate'),true).'/'.$hash;
                    
                    if($this->User->saveField('tokenhash',$hash)){
                        $email = Emails::getRegisterMail($url, $userEmail);
                        if ($email->send()) {
                            $this->Session->setFlash(__('New user created. An email has sent to you to activate your account.'));
                        } else {
                            $this->Session->setFlash(__('Error on sending email!'));
                        }
                        return $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Session->setFlash(__('Error on creating user.'));
                    }
                    
                }
                $this->Session->setFlash(__('Unable to add user.'));
            } else {
                $this->Session->setFlash(__('User with this email already exists'));
            }
        }
    }
    
    public function activate($tokenhash) {
        $user = $this->User->findByTokenhash($tokenhash);
        if (count($user) === 1) {
            $user['User']['tokenhash']=NULL;
            $user['User']['verified_email']=1;
            $this->User->save($user);
            $this->Session->setFlash(__('Account successfully activated.'));
        } else {
            $this->Session->setFlash(__('Account activation failed.'));
        }
        return $this->redirect(array('action' => 'index'));
    }
    
    public function connectToPad($padId) {
        $pad = parent::splitPadId($padId);
        $this->loadModel('Group');
        if ($this->Group->isUserMemberOfGroup($this->Auth->user('id'), $pad->groupName)) {
            $user = $this->User->findById($this->Auth->user('id'));
            $authorId = $this->eplite->createAuthorIfNotExistsFor($this->Auth->user('id'), $user['User']['name'])->authorID;
            $validUntil = time() + 7200;
            $session = $this->eplite->createSession($pad->groupName, $authorId, $validUntil)->sessionID;
            setcookie('sessionID', $session, $validUntil, "/");
            
            return $this->redirect(array('controller' => 'pads', 'action' => 'show', $padId));
            //return $this->redirect($url);
        }
    }
    
    public function deleteSession($userId) {
        $authorId = $this->eplite->createAuthorIfNotExistsFor($userId, "")->authorID;
        $sessions = $this->eplite->listSessionsOfAuthor($authorId);
        foreach($sessions as $sessionKey => $sessionVal) {
            $this->eplite->deleteSession($sessionKey);
        }
    }
    
    /*public function deleteSessionForGroup($userId, $mappingId) {
        
    
        $authorId = $this->eplite->createAuthorIfNotExistsFor($userId, "")->authorID;
        $sessions = $this->eplite->listSessionsOfAuthor($authorId);
        foreach($sessions as $sessionKey => $sessionVal) {
            $this->eplite->deleteSession($sessionKey);
        }
    }*/
    
    public function delete() {
        debug($this->Auth->user('id'));
        $this->Session->setFlash(__('Account deleted.'));
        $this->User->delete($this->Auth->user('id'));
        $this->logout();
    }
}

?>