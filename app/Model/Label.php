<?php
class Label extends AppModel
{
    public $useTable = 'tabel_labels';

    public $primaryKey = 'id_label';

    public $belongsTo = [
        'Komentar' => ['foreignKey'=>'id_komen']
    ];

    public $hasOne = [
        'User' => ['foreignKey'=>'email']
    ];
}
