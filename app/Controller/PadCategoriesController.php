<?php

class PadCategoriesController extends AppController {
    public function manage($mappingId) {
        $this->loadModel('Group');
        $groupId = $this->Group->getGroupId($mappingId);
        if ($groupId) {
            $categories = $this->Category->findByGroupId($groupId);
            $this->set('categories', $categories);
            $this->set('mappingId', $mappingId);
        }
    }
    
    public function show($mappingId, $categoryId) {
        $this->loadModel('Pad');
        if ($mappingId && $categoryId) {
            
        }
    }
    
    public function saveMapping($mappingId) {
        if ($this->request->is('post')) {
            $requestData = $this->request->data['PadCategory'];
            if ($requestData['categoryMapping']) {
                $categoryMapping = json_decode($requestData['categoryMapping']);
                debug($categoryMapping);
                foreach ($categoryMapping as $padId => $categories) {
                    $this->saveCategoriesForPad($padId, $categories);
                }
            }
        }
        return $this->redirect(array('controller' => 'Categories', 'action' => 'manage', $mappingId));
    }
    
    private function saveCategoriesForPad($padId, $categories) {
        $groupId = parent::splitPadId($padId)->groupName;
        $this->loadModel('Group');
        if ($this->Group->isUserMemberOfGroup($this->Auth->user('id'), $groupId)) {
            // TODO: delete all padCategories for group
            $this->PadCategory->deleteAll(array('PadCategory.pad_id' => $padId));
            foreach ($categories as $category) {                
                $this->PadCategory->create();
                $this->PadCategory->save(array('PadCategory' => array('pad_id' => $padId, 'category_id' => $category)));
                debug($this->PadCategory->validationErrors);
            }
        }
    }
}