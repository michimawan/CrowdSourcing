<?php
App::uses('AuthComponent', 'Controller/Component');

class User extends AppModel
{
    public $primaryKey = 'email';

    public $hasMany = [
        'Label' => [
            'className' => 'Label',
            'foreignKey'=> 'username_pelabel'
        ]
    ];
}
