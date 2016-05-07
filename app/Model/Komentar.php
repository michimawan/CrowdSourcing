<?php
class Komentar extends AppModel
{
    public $useTable = 'komentar_statuses';

    public $actsAs = ['Containable'];

    //declare no primary key
    public $primaryKey = 'id_komentar';

    public $belongsTo = [
        'Status' => [
            'className' => 'Status',
            'foreignKey' => 'id_status'
        ]
    ];

    public $hasMany = [
        'Label' => [
            'className' => 'Label',
            'foreignKey'=>'id_komen'
        ]
    ];

    public function getRandom($user, $n)
    {
        $q = "SELECT * FROM `komentar_statuses` AS `Komentar`
            LEFT JOIN `statuses` AS `Status` ON (`Komentar`.`id_status` = `Status`.`id_status`)
            WHERE id_komentar NOT IN
            (SELECT id_komen FROM tabel_labels WHERE username_pelabel LIKE '$user')
            AND id_komentar NOT IN
            (SELECT `tabel_labels`.id_komen FROM tabel_labels WHERE 1 GROUP BY `tabel_labels`.id_komen HAVING count(`tabel_labels`.id_komen) = '$n')
            ORDER BY RAND()
            LIMIT 1";

        return $this->query($q);
    }

    public function countUsers($iduser)
    {
        $q = "SELECT COUNT(`tabel_labels`.id_label) as jumlah
            FROM `tabel_labels`
            WHERE username_pelabel LIKE
            (SELECT email FROM users WHERE social_network_id = '$iduser')";

        return $this->query($q);
    }

    public function getMaxJmlLabel()
    {
        $q = "SELECT MAX(jml_label) as maxi FROM komentar_statuses";

        return $this->query($q);
    }

    public function updateStatus($label, $n)
    {
        $q = "UPDATE `komentar_statuses` SET `status`='$label' WHERE jml_label = '$n'";

        return $this->query($q);
    }

    public function getStatus($id)
    {
        $q = "SELECT * FROM `statuses` AS `Status`
            LEFT JOIN `komentar_statuses` AS `Komentar` ON `Komentar`.id_status = `Status`.id_status
            WHERE `Status`.id_status = '$id'";

        return $this->query($q);
    }

    public function getKomen()
    {
        $q = "SELECT * FROM `komentar_statuses` AS `Komentar`
            LEFT JOIN `statuses` AS `Status` ON `Komentar`.id_status = `Status`.id_status
            ORDER BY `Komentar`.id_status";

        return $this->query($q);
    }
}
