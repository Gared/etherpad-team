<?php
App::uses('AppModel', 'Model');
/**
 * PadCategory Model
 *
 */
class PadCategory extends AppModel {

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'pad_category_id';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'pad_category_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'category_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'pad_id' => array(
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
	
    public function getCategoriesForPads($padIds) {
        $padCategories = $this->find('all', array(
            'joins' => array( 
                    array(
                        'table' => 'categories',
                        'alias' => 'Category',  
                        'conditions'=> array('Category.id = PadCategory.category_id') 
                    )
                ),
            'where' => array('pad_id' => $padIds),
            'fields' => array('Category.id, Category.name, Category.color, pad_id')
        ));
        return $padCategories;
    }
}
