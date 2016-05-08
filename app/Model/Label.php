<?php
class Label extends AppModel
{
    public $useTable = 'tabel_labels';

    public $primaryKey = 'id_label';

    public $belongsTo = [
        'Komentar' => ['foreignKey'=>'id_komen'],
        'User' => ['foreignKey'=>'username_pelabel']
    ];
}
