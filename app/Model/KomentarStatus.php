<?php
class KomentarStatus extends AppModel {
	public $actsAs = array('Containable');
	//declare no primary key
	public $primaryKey = 'id_komentar';
	//define many to one
	public $belongsTo = array(
		'Status' => array(
            'className' => 'Status',
            'foreignKey' => 'id_status'
        )
    );

	//define one to many
	public $hasMany = array(
		'TabelLabel' => array(
			'className' => 'TabelLabel',
			'foreignKey'=>'id_komen'
		)
	);

	public function getrandom($user, $n){
		
		$q = "SELECT * FROM `komentar_statuses` AS `KomentarStatus`
			LEFT JOIN `statuses` AS `Status` ON (`KomentarStatus`.`id_status` = `Status`.`id_status`)
			WHERE id_komentar NOT IN
				(SELECT id_komen FROM tabel_labels WHERE username_pelabel LIKE '$user') 
			AND id_komentar NOT IN 
				(SELECT `tabel_labels`.id_komen FROM tabel_labels WHERE 1 GROUP BY `tabel_labels`.id_komen HAVING count(`tabel_labels`.id_komen) = '$n')
			ORDER BY RAND()
			LIMIT 1";
		
		return $this->query($q);
	}

	public function countusers($iduser){
		$q = "SELECT COUNT(`tabel_labels`.id_label) as jumlah 
			FROM `tabel_labels` 
			WHERE username_pelabel LIKE 
				(SELECT email FROM users WHERE social_network_id = '$iduser')";

		return $this->query($q);
	}

	public function getMaxJmlLabel(){
		$q = "SELECT MAX(jml_label) as maxi FROM komentar_statuses";
		//$q = "SELECT * FROM `komentar_statuses` WHERE jml_label > '$label'";

		return $this->query($q);
	}

	public function updatestatus($label, $n){
		$q = "UPDATE `komentar_statuses` SET `status`='$label' WHERE jml_label = '$n'";

		return $this->query($q);
	}

	public function getStatus($id){
		$q = "SELECT * FROM `statuses` AS `Status`
			LEFT JOIN `komentar_statuses` AS `KomentarStatus` ON `KomentarStatus`.id_status = `Status`.id_status 
			WHERE `Status`.id_status = '$id'";

		return $this->query($q);
	}

	public function getKomen(){
		/*
		$q = "SELECT * FROM `komentar_statuses` AS `KomentarStatus`
			LEFT JOIN `statuses` AS `Status` ON `KomentarStatus`.id_status = `Status`.id_status 
			LEFT JOIN tabel_labels AS `TabelLabel` ON `KomentarStatus`.id_komentar = `TabelLabel`.id_komen 
			ORDER BY `KomentarStatus`.id_komentar";
		*/
		$q = "SELECT * FROM `komentar_statuses` AS `KomentarStatus`
			LEFT JOIN `statuses` AS `Status` ON `KomentarStatus`.id_status = `Status`.id_status 
			ORDER BY `KomentarStatus`.id_status";

		return $this->query($q);
	}
}
/*
SELECT * FROM komentar_statuses 
WHERE id_komentar NOT IN
	(SELECT id_komen FROM tabel_labels WHERE username_pelabel LIKE 'openpublick@gmail.com') 
AND id_komentar NOT IN 
	(SELECT tabel_labels.id_komen FROM tabel_labels WHERE 1 GROUP BY tabel_labels.id_komen HAVING count(tabel_labels.id_komen) < 5) 
LIMIT 1 
*/
?>