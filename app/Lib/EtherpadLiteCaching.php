<?php

class EtherpadLiteCaching {
    protected $eplite = null;

    public function __construct($eplite) {
        $this->eplite = $eplite;
    }
    
    public function createGroup(){
        Cache::delete('groups', 'eplite');
        return $this->eplite->createGroup();
    }

    public function createGroupIfNotExistsFor($groupMapper){
        Cache::delete('groups', 'eplite');
        return $this->eplite->createGroupIfNotExistsFor($groupMapper);
    }

  // deletes a group 
  public function deleteGroup($groupID){
    $return = $this->eplite->deleteGroup($groupID);
    Cache::delete($groupID, 'eplite');
    return $return;
  }

  // returns all pads of this group
  public function listPads($groupID){
    if (($pads = Cache::read($groupID.'.pads', 'eplite')) === false) {
        $pads = $this->eplite->listPads($groupID);
        Cache::write($groupID.'.pads', $pads, 'eplite');
    }
    return $pads;
  }

  // creates a new pad in this group 
  public function createGroupPad($groupID, $padName, $text){
    return $this->eplite->createGroupPad($groupID, $padName, $text);
  }

  // list all groups
  public function listAllGroups(){
    if (($groups = Cache::read('groups', 'eplite')) === false) {
        $groups = $this->eplite->listAllGroups();
        Cache::write('groups', $groups, 'eplite');
    }
    return $groups;
  }

  // AUTHORS
  // Theses authors are bind to the attributes the users choose (color and name). 

  // creates a new author 
  public function createAuthor($name){
    return $this->eplite->createAuthor($name);
  }

  // this functions helps you to map your application author ids to etherpad lite author ids 
  public function createAuthorIfNotExistsFor($authorMapper, $name){
    return $this->eplite->createAuthorIfNotExistsFor($authorMapper, $name);
  }

  // returns the ids of all pads this author as edited
  public function listPadsOfAuthor($authorID){
    return $this->eplite->listPadsOfAuthor($authorID);
  }

  // Gets an author's name
  public function getAuthorName($authorID){
    if (($authorName = Cache::read($authorID.'.authorName', 'eplite')) === false) {
        $authorName = $this->eplite->getAuthorName($authorID);
        Cache::write($authorID.'.authorName', $authorName, 'eplite');
    }
    return $authorName;
  }

  // SESSIONS
  // Sessions can be created between a group and a author. This allows
  // an author to access more than one group. The sessionID will be set as
  // a cookie to the client and is valid until a certian date.

  // creates a new session 
  public function createSession($groupID, $authorID, $validUntil){
    return $this->eplite->createSession($groupID, $authorID, $validUntil);
  }

  // deletes a session 
  public function deleteSession($sessionID){
    return $this->eplite->deleteSession($sessionID);
  }

  // returns informations about a session 
  public function getSessionInfo($sessionID){
    return $this->eplite->createGroupIfNotExistsFor($groupMapper);
  }

  // returns all sessions of a group 
  public function listSessionsOfGroup($groupID){
    return $this->eplite->listSessionsOfGroup($groupID);
  }

  // returns all sessions of an author 
  public function listSessionsOfAuthor($authorID){
    return $this->eplite->listSessionsOfAuthor($authorID);
  }

  // PAD CONTENT
  // Pad content can be updated and retrieved through the API

  // returns the text of a pad 
  public function getText($padID, $rev=null){
    if (($text = Cache::read($padID.'.text'.$rev, 'eplite')) === false) {
        $text = $this->eplite->getText($padID, $rev);
        Cache::write($padID.'.text'.$rev, $text, 'eplite');
    }
    return $text;
  }

  // returns the text of a pad as html
  public function getHTML($padID, $rev=null){
    if (($html = Cache::read($padID.'.html'.$rev, 'eplite')) === false) {
        $html = $this->eplite->getHTML($padID, $rev);
        Cache::write($padID.'.html'.$rev, $html, 'eplite');
    }
    return $html;
  }

  // sets the text of a pad 
  public function setText($padID, $text){
    $return = $this->eplite->setText($padID, $text);
    Cache::write($padID.'.text', $this->eplite->getText($padID, $rev), 'eplite');
    return $return;
  }

  // sets the html text of a pad 
  public function setHTML($padID, $html){
    $return = $this->eplite->setHTML($padID, $html);
    Cache::write($padID.'.html', $this->eplite->getHTML($padID, $rev), 'eplite');
    return $return;
  }

  // PAD
  // Group pads are normal pads, but with the name schema
  // GROUPID$PADNAME. A security manager controls access of them and its
  // forbidden for normal pads to include a $ in the name.

  // creates a new pad
  public function createPad($padID, $text){
    $return = $this->eplite->createPad($padID, $text);
    Cache::write($padID.'.text', $text, 'eplite');
    return $return;
  }

  // returns the number of revisions of this pad 
  public function getRevisionsCount($padID){
    return $this->eplite->getRevisionsCount($padID);
  }

  // returns the number of users currently editing this pad
  public function padUsersCount($padID){
    if (($padUsersCount = Cache::read($padID.'.padUsersCount', 'eplite')) === false) {
        $padUsersCount = $this->eplite->padUsersCount($padID);
        Cache::write($padID.'.padUsersCount', $padUsersCount, 'eplite');
    }
    return $padUsersCount;
  }

  // return the time the pad was last edited as a Unix timestamp
  public function getLastEdited($padID){
    return $this->eplite->getLastEdited($padID);
  }

  // deletes a pad 
  public function deletePad($padID){
    $return = $this->eplite->deletePad($padID);
    Cache::delete($padID, 'eplite');
    return $return;
  }

  // returns the read only link of a pad 
  public function getReadOnlyID($padID){
    return $this->eplite->getReadOnlyID($padID);
  }

  // returns the ids of all authors who've edited this pad
  public function listAuthorsOfPad($padID){
    return $this->eplite->listAuthorsOfPad($padID);
  }

  // sets a boolean for the public status of a pad 
  public function setPublicStatus($padID, $publicStatus){
    $return = $this->eplite->setPublicStatus($padID, $publicStatus);
    Cache::write($padID.'.publicStatus', $this->eplite->getPublicStatus($padID), 'eplite');
    return $return;
  }

  // return true of false 
  public function getPublicStatus($padID){
    if (($publicStatus = Cache::read($padID.'.publicStatus', 'eplite')) === false) {
        $publicStatus = $this->eplite->getPublicStatus($padID);
        Cache::write($padID.'.publicStatus', $publicStatus, 'eplite');
    }
    return $publicStatus;
  }

  // returns ok or a error message 
    public function setPassword($padID, $password){
        $return = $this->eplite->setPassword($padID, $password);
        Cache::write($padID.'.passwordProtected', true, 'eplite');
        return $return;
    }

  // returns true or false 
  public function isPasswordProtected($padID){
    if (($passwordProtected = Cache::read($padID.'.passwordProtected', 'eplite')) === false) {
        $passwordProtected = $this->eplite->isPasswordProtected($padID);
        Cache::write($padID.'.passwordProtected', $passwordProtected, 'eplite');
    }
    return $passwordProtected;
  }

  // Get pad users
  public function padUsers($padID){
    if (($padUsers = Cache::read($padID.'.padUsers', 'eplite')) === false) {
        $padUsers = $this->eplite->padUsers($padID);
        Cache::write($padID.'.padUsers', $padUsers, 'eplite');
    }
    return $padUsers;
  }

  // Send all clients a message
  public function sendClientsMessage($padID, $msg){
    return $this->eplite->sendClientsMessage($padID, $msg);
  }
}







