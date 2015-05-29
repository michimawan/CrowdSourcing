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
}