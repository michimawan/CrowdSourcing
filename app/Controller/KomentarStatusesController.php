
<?php
class KomentarStatusesController extends AppController {
	public $uses = array('KomentarStatus');
	public $layout = "layout";

	public function index($id = null) {
		if($id){
			$this->set('title','Daftar Komentar Facebook');
			$datas = $this->KomentarStatus->find(
				'all', array('conditions' => array(
					'KomentarStatus.id_komentar' => $id)
				)
			);
			$this->set(compact("datas"));
		} else {
			$this->redirect(array('controller' => 'Statuses', 'action' => 'index'));
		}
		
	}


}
?>