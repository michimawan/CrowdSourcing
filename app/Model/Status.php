<?php
class Status extends AppModel {
	//declare custom primary key, default PK is id
	public $primaryKey = 'id_status';

	//define one to many
	public $hasMany = array(
		'KomentarStatus' => array(
			'className' => 'KomentarStatus',
			'order' => 'KomentarStatus.waktu_komen ASC',
			'foreignKey'=>'id_status'
		)
	);
	
}
?>