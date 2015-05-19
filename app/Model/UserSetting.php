<?php
class UserSetting extends AppModel {
	public $actsAs = array('Containable');
	//define many to one
	public $belongsTo = array(
		'Setting' => array('foreignKey'=>'id_setting'),
		'User' => array('foreignKey'=>'id_user'),
	);
}
?>