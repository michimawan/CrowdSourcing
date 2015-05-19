<?php
App::uses('File', 'Utility');

class StatusesController extends AppController {
	public $uses = array('Status', 'KomentarStatus');
	public $layout = "layout";

	public $components = array('Paginator');

	// admin only
	public function index() {
		$this->set('title','Daftar Status Facebook');
		
		/*
		$datas = $this->Status->find(
			'all', array('recursive' => 0)
		);
		*/
		
		$this->Paginator->settings = array(
			'limit' => 5,
			'recursive' => 0,
			'paramType' => 'querystring',
			'limit' => 5,
			'maxLimit' => 100,
		);

		$datas = $this->Paginator->paginate('Status');
		
		$this->set(compact("datas"));
	}

	// admin only
	public function view($id = null){
		if($id){
			$this->set('title','Status Facebook');
			/*
			$datas = $this->Status->find(
				'all', array('conditions' => array(
					'Status.id_status' => $id),
					'recursive' => 1
				)
			);
			$this->set(compact("datas"));
			*/
			$status = $this->Status->find('all', array(
				'conditions' => array('Status.id_status' => $id),
				'recursive' => 0)
			);

			$this->set(compact('status'));
			$this->Paginator->settings = array(
			'conditions' => array('KomentarStatus.id_status' => $id),
			'recursive' => -1,
			'paramType' => 'querystring',
			'limit' => 5,
			'maxLimit' => 100,
			);

			$komentars = $this->Paginator->paginate('KomentarStatus');

			$this->set(compact("komentars"));


		} else {
			$this->redirect(array('action' => 'index'));
		}
	}

	//JSON -------------------------------------------------------
	public function detail($id)
	{
		
		$this->autoRender = false;
		if($this->request->is('ajax'))
		{
			$datas = $this->Status->KomentarStatus->find(
				'all', array(
					'conditions' => array('KomentarStatus.id_komentar' => $id),
					'contain' => array('TabelLabel.id_label', 'TabelLabel.username_pelabel', 'TabelLabel.nama_label')
				)
			);
			
			if($datas)
			{
				echo json_encode($datas);
			}
			else
			{
				echo "no";
			}
		}
		else
		{
			$this->redirect(array('action'=>'index'));
		}
		
	}

	public function randomkomentar($id){
		//baca maksimum label per komentar
		$file = new File(WWW_ROOT .  DS .'files'.DS .'setting.txt');
		$json = $file->read(true, 'r');
		$json = json_decode($json);
		$maxlabel = $json->n;

		//ambil username user
		$users = $this->KomentarStatus->TabelLabel->User->find('first', 
			array('conditions' => array('User.id' => $id),
				'fields' => array('User.username'),
				'recursive' => -1
			)
		);
		$users = $users['User']['username'];
		//$this->set(compact('users'));

		//ambil daftar komentar yang sudah dilabeli user terkait dari tabel label
		$id_komentar_terlabel = $this->KomentarStatus->TabelLabel->find(
			'all', array(
				'conditions' => array('TabelLabel.username_pelabel' => $users),
				'fields' => array('TabelLabel.id_komen'),
				'recursive' => -1
			)
		);
		//simpan dalam array
		$terlabel = Array();
		foreach ($id_komentar_terlabel as $data) {
			$terlabel[] = $data['TabelLabel']['id_komen'];
		}

		//ambil 5 id komentar dari tabel komentar yang belum pernah dilabeli user berdasar id_komentar terlabel
		$id_komen = $this->KomentarStatus->find('list', array(
			'conditions' => array(
				'NOT' => array('KomentarStatus.id_komentar' => $terlabel)),
			'fields' => array('KomentarStatus.id_komentar'),
			'order' => 'RAND()',
			'limit' => 5
			)
		);
		$id_komen = $id_komen[array_rand($id_komen)];
		//ambil data terkait id_komen terpilih
		$this->set('title','Daftar Komentar Facebook');
		$datas = $this->KomentarStatus->find(
			'all', array('conditions' => array(
				'KomentarStatus.id_komentar' => $id_komen),
				'recursive'=> 0
			)
		);

		return $datas;
	}

	public function labeling($id = null){
		if($this->request->is('post')) {
			/*
			$this->log('Got here', 'debug');
			$dd = $this->request->data;
			//random lagi
			$this->set(compact('dd'));
			//$data = array('nama_label' => $id_label);
			//$this->KomentarStatus->TabelLabel->id = $id_komentar;
			//$this->KomentarStatus->TabelLabel->save($data);
			
			$this->set('title','Daftar Komentar Facebook');
			$datas = $this->KomentarStatus->find(
				'all', array('conditions' => array(
					'KomentarStatus.id_komentar' => '10152085258656179_10152085259916179'),
					'recursive'=> 0
				)
			);
			*/
			$this->set(compact('datas'));
			$this->redirect(array('action' => 'labeling', $id));
		} else if ($id) {
			$datas = $this->randomkomentar($id);
			$this->set(compact('datas'));
		}
	}

	public function edit($id_komentar = null, $id_label = null){
		$this->set('title', 'Edit Label Komentar');

		if($this->request->is('post')){

			$data = array('nama_label' => $id_label);
			$this->KomentarStatus->TabelLabel->id = $id_komentar;
			$this->KomentarStatus->TabelLabel->save($data);
			
			return $this->redirect(array('controller' => 'Users', 'action' => 'user'));
		} else if($id_komentar != null && $id_label != null){
			$this->set('title','Status Facebook');
			
			$datas = $this->Status->KomentarStatus->find(
				'all', array('conditions' => array('KomentarStatus.id_komentar' => $id_komentar),
					'recursive' => 0
				)
			);
			
			$this->set(compact("datas"));

			$labels = $this->KomentarStatus->TabelLabel->findByIdLabel($id_label);
			$this->set(compact("labels"));			
		} else {
			$this->redirect(array('action' => 'index'));
		}
	}


	public function countlabel(){
		return $this->Status->KomentarStatus->TabelLabel->find(
			'count', array('fields' => array('TabelLabel.id_label'))
		);
	}

	public function countkomentar(){
		return $this->Status->KomentarStatus->find(
			'count', array('fields' => array('KomentarStatus.id_komentar'))
		);
	}
}
?>