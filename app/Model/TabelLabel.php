<?php
class TabelLabel extends AppModel {
	public $primaryKey = 'id_label';

	//definisi many to one
	public $belongsTo = array(
		'KomentarStatus' => array('foreignKey'=>'id_komen')
	);
	
	public $hasOne = array(
		'User' => array('foreignKey'=>'username')
	);
}
?>