<?php
App::uses('File', 'Utility');

class StatusesController extends AppController {
	public $uses = array('Status', 'KomentarStatus');
	public $layout = "layout";
	public $helpers = array('Html', 'Form', 'Csv'); 
	public $components = array('Paginator');

	// priviledge: admin only
	// method for showing all status
	public function index() {
		if($this->Auth->user()['role']=='user')
			$this->redirect(array('controller' => 'Users', 'action' => 'user'));

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

	// priviledge: admin only
	// method for showing all a single status and all comment related to it
	public function view($id = null){
		if($this->Auth->user()['role']=='user')
			$this->redirect(array('controller' => 'Users', 'action' => 'user'));

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
			'recursive' => 1,
			'paramType' => 'querystring',
			'limit' => 5,
			'maxLimit' => 100,
			);

			$komentars = $this->Paginator->paginate('KomentarStatus');

			$this->set(compact("komentars"));

			$maxlabel = $this->getN();
			$this->set(compact('maxlabel'));
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


	// priviledge: this class
	// method for getting a random comment to be labelled by user
	private function randkomentar($id){
		//baca maksimum label per komentar
		/*
		$file = new File(WWW_ROOT .  DS .'files'.DS .'setting.txt');
		$json = $file->read(true, 'r');
		$json = json_decode($json);
		$maxlabel = $json->n;
		*/
		$maxlabel = $this->getN();
		//ambil username user
		$users = $this->KomentarStatus->TabelLabel->User->find('first', 
			array('conditions' => array('User.social_network_id' => $id),
				'fields' => array('User.email'),
				'recursive' => -1
			)
		);
		$users = $users['User']['email'];
		
		$datas = $this->KomentarStatus->getrandom($users, $maxlabel);
		return $datas;
	}

	/*
	private function randomkomentar($id){
		//baca maksimum label per komentar
		$file = new File(WWW_ROOT .  DS .'files'.DS .'setting.txt');
		$json = $file->read(true, 'r');
		$json = json_decode($json);
		$maxlabel = $json->n;

		//ambil username user
		$users = $this->KomentarStatus->TabelLabel->User->find('first', 
			array('conditions' => array('User.social_network_id' => $id),
				'fields' => array('User.email'),
				'recursive' => -1
			)
		);
		$users = $users['User']['email'];
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

			//batasi jumlah label di sini

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
	*/

	// priviledge: user 
	// method for user can labeling a comment or not
	public function labeling($id = null, $user = null, $id_komen = null, $id_status = null, $label = null){
		if($this->Auth->user()['role']=='admin')
			$this->redirect(array('controller' => 'Users', 'action' => 'index'));

		//hitung total komentar
		$komentars = $this->KomentarStatus->find('count');

		//lihat user sudah melabeli berapa komentar
		$labelsekarang = $this->KomentarStatus->countusers($id);
		//buat permissionnya
		$tambahlabel = false;
		if($labelsekarang[0][0]['jumlah'] == $komentars)
			$tambahlabel = true;
			
		if($tambahlabel == true)
			$this->redirect(array('controller' => 'Users', 'action' => 'user'));

		$id = $this->Auth->user()['social_network_id'];

		if($this->request->is('post')) {
			
			//cek jml_label di komen, apa sudah sesuai N ? kl sudah, set flash gagal
			if($this->ceklabelkomennow($id_komen))
				$this->Session->setFlash("Komentar '$id_komen' gagal dilabeli", 'customflash', array('class' => 'warning'));
			else{
				//kalau belum jalankan update
				$data['TabelLabel']['id_status'] = $id_status;
				$data['TabelLabel']['id_komen'] = $id_komen;
				$data['TabelLabel']['username_pelabel'] = $user;
				$data['TabelLabel']['nama_label'] = $label;

				$this->KomentarStatus->TabelLabel->save($data);

				App::import('Controller', 'Users');
		    	$UsersController = new UsersController;
				$UsersController->incrementLabel($id);

				$this->inckomenlabel($id_komen);
				$this->Session->setFlash("Komentar '$id_komen' berhasil dilabeli", 'customflash', array('class' => 'success'));
			}
			$this->redirect(array('action' => 'labeling', $id));
		} else if ($id) {
			$this->set('title', 'Pelabelan Komentar');
			$datas = $this->randkomentar($id);
			if($datas)
				$this->set(compact('datas'));
			else 
				$this->redirect(array('controller' => 'Users', 'action' => 'user', $id));
			//$log = $this->Status->getDataSource()->getLog(false, false);
			//debug($log);
		}
	}

	// priviledge: user 
	// method for user edit label that has given to a comment
	public function edit($id_komentar = null, $id_label = null){
		if($this->Auth->user()['role']=='admin')
			$this->redirect(array('controller' => 'Users', 'action' => 'index'));

		$this->set('title', 'Edit Label Komentar');
		if($this->getLock() == 'true')
			$this->redirect(array('controller' => 'Users', 'action' => 'user'));

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
 
	// method for export all comment and it's status
	public function expKomentar(){
		$datas = $this->Status->KomentarStatus->getKomen();
		$this->set(compact('datas'));
	    $this->layout = null;

	    $this->autoLayout = false;
	}

	// method for export single status, and all comment that related to it
	public function expStatus($id){
		$datas = $this->Status->KomentarStatus->getStatus($id);
		$this->set(compact('datas'));
	    $this->layout = null;

	    $this->autoLayout = false;
	}

	// method for count the final label that will be given to a comment
	public function calculate(){
		$datas = $this->Status->KomentarStatus->find('all');
		$this->set(compact('datas'));

		foreach($datas as $data){
			$value = 0;
			foreach ($data['TabelLabel'] as $label) {
				if($label['nama_label'] == 'positif')
					$value++;
				else if($label['nama_label'] == 'negatif')
					$value--;
			}
			
			if($value > 0)
				$value = 'positif';
			else if($value == 0)
				$value = 'netral';
			else 
				$value = 'negatif';

			$label = array('id_komentar' => $data['KomentarStatus']['id_komentar'], 'label' => $value);
			$this->Status->KomentarStatus->save($label);
		}
		$this->redirect(array('controller' => 'users', 'action' => 'index'));
	}

	// method for get total number of labels that have been made by all user
	public function countlabel(){
		return $this->Status->KomentarStatus->TabelLabel->find(
			'count', array('fields' => array('TabelLabel.id_label'))
		);
	}

	// method for count how many comments that exist in db
	public function countkomentar(){
		return $this->Status->KomentarStatus->find(
			'count', array('fields' => array('KomentarStatus.id_komentar'))
		);
	}


	// method for increment number of label that has been given at certain comment.
	// this method also update the status of a comment, if labels count is the same as configuration file
	private function inckomenlabel($id) {
		$this->Status->KomentarStatus->updateAll(
	        array('KomentarStatus.jml_label' => 'KomentarStatus.jml_label+1'),                    
	        array('KomentarStatus.id_komentar' => $id)
    	);

    	$jmllabel = $this->Status->KomentarStatus->find('first', array(
			'conditions' => array('KomentarStatus.id_komentar' => $id),
			'fields' => 'KomentarStatus.jml_label',
			'recursive' => -1
			)
		);
		/*
    	debug($jmllabel['KomentarStatus']['jml_label']);
    	debug($this->getN());
    	debug($jmllabel);
    	debug($this->getN());
    	*/
		//kalau sudah sama dengan N, update statusnya

		if($jmllabel['KomentarStatus']['jml_label'] == $this->getN()){
			$data = array('id_komentar' => $id, 'status' => 'lengkap');
			// This will update Recipe with id 10
			$this->Status->KomentarStatus->save($data);
		}
	}

	// method for check the label of a comment
	// return true if the status = 'lengkap', else will be return false
	private function ceklabelkomennow($id){
		$statuslabel = $this->Status->KomentarStatus->find('first', array(
			'conditions' => array('KomentarStatus.id_komentar' => $id),
			'fields' => 'KomentarStatus.status',
			'recursive' => -1
			)
		);

		if($statuslabel['KomentarStatus']['status'] == 'lengkap')
			return true;
		else 
			return false;
	}
	
	// method for get the max number of label for a comment
	private function getN(){
		$file = new File(WWW_ROOT .  DS .'files'.DS .'setting.txt');
		$json = $file->read(true, 'r');
		$json = json_decode($json);
		return $json->n;
	}

	// method for get the current configuration for locking state
	private function getLock(){
		//baca maksimum label per komentar
		$file = new File(WWW_ROOT .  DS .'files'.DS .'setting.txt');
		$json = $file->read(true, 'r');
		$json = json_decode($json);
		return $json->lock;
	}
}
?>