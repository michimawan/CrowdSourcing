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
}
?>