<?php

class CategoriesController extends AppController {
    public function manage($mappingId) {
        $this->loadModel('Group');
        $this->loadModel('Pad');
        $this->loadModel('PadCategory');
        $groupId = $this->Group->getGroupId($mappingId);
        if ($groupId) {
            $categories = $this->Category->findAllByGroupId($groupId);
            $groupName = $this->Group->getGroupName($mappingId);
            $pads = $this->getListOfPads($mappingId);

            $padCategories = $this->getCategoriesForPads($pads);
            $categoryMappingObject = $this->getCategoryMappingObject($padCategories);
            
            $this->set('pads', $pads);
            $this->set('groupName', $groupName);
            $this->set('categories', $categories);
            $this->set('mappingId', $mappingId);
            $this->set('padcategories', $padCategories);
            $this->set('categoryMapping', json_encode($categoryMappingObject));
            $this->setLayoutParameters(__("Manage categories"), $groupName, $mappingId);
        }
    }
    
    private function getListOfPads($mappingId) {
        $groupIds = $this->eplite->listAllGroups()->groupIDs;
        $pads = array();
        // check if group really exists on eplite server
        if (in_array($mappingId, $groupIds)) {
            $padIds = $this->eplite->listPads($mappingId)->padIDs;
            foreach($padIds as $padId) {
                $pads[] = parent::splitPadId($padId);
            }
        }
        return $pads;
    }
    
    private function getCategoriesForPads($pads) {
        $padIds = array();
        $resultArray = array();
        
        foreach ($pads as $pad) {
            $padIds[] = $pad->padName;
        }
        foreach ($this->PadCategory->getCategoriesForPads($padIds) as $padCategory) {
            $resultArray[$padCategory['PadCategory']['pad_id']][] =  
                    array(
                        'Category' => $padCategory['Category']
                    );
        }
        return $resultArray;
    }
    
    private function getCategoryMappingObject($padCategories) {
        $result = new stdClass();
        foreach ($padCategories as $padCategoryKey => $padCategory) {
            foreach ($padCategory as $category) {
                $result->{$padCategoryKey}[] = $category['Category']['id'];
            }
        }
        return $result;
    }
    
    public function create() {
        $mappingId = "";
        if ($this->request->is('post')) {
            $requestData = $this->request->data['Category'];
            $mappingId = $requestData['mappingId'];
            $name = $requestData['name'];
            $color = $requestData['color'];
            
            $this->loadModel('Group');
            $groupId = $this->Group->getGroupId($mappingId);
            if ($groupId) {
                debug($this->Category->save(array('name' => $name, 'color' => $color, 'group_id' => $groupId)));
            }
        }
        return $this->redirect(array('action' => 'manage', $mappingId));
    }
    
    public function delete($mappingId, $categoryId) {
        if ($categoryId) {
            $this->Category->delete(array('id' => $categoryId));
        }
        return $this->redirect(array('action' => 'manage', $mappingId));
    }
    
    public function show() {
        $mappingId = "";
        if ($this->request->is('post')) {
            $requestData = $this->request->data['Category'];
            $mappingId = $requestData['mappingId'];
            $name = $requestData['name'];
            $color = $requestData['color'];
            
            $this->loadModel('Group');
            $groupId = $this->Group->getGroupId($mappingId);
            if ($groupId) {
                $this->Category->save(array('name' => $name, 'color' => $color, 'group_id' => $groupId));
            }
        }
        return $this->redirect(array('action' => 'manage', $mappingId));
    }
}