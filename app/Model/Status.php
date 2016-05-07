<?php
class Status extends AppModel
{
    public $useTable = 'statuses';

    //declare custom primary key, default PK is id
    public $primaryKey = 'id_status';

    public $hasMany = [
        'Komentar' => [
            'className' => 'Komentar',
            'order' => 'Komentar.waktu_komen ASC',
            'foreignKey'=>'id_status'
        ]
    ];
}
