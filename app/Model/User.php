<?php
App::uses('AuthComponent', 'Controller/Component');
class User extends AppModel {
    public $primaryKey = 'email';

    public $hasMany = array(
        'TabelLabel' => array(
            'className' => 'TabelLabel',
            'foreignKey'=> 'username_pelabel'
        )
    );

    /**
     * Before Save
     * @param array $options
     * @return boolean
     */
     public function beforeSave($options = array()) {
     
        // fallback to our parent
        return parent::beforeSave($options);
    }
}