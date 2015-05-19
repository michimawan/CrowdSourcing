<?php
App::uses('File', 'Utility');

class UsersController extends AppController {
	public $layout = "layout";
	/*bisa overwrite juga koq*/
	
	public $uses = array('User', 'TabelLabel'); 
	public $components = array('Paginator');
	// admin only
	public function index() {
		$this->set('title','Daftar Pengguna Facebook Crowdsourcing');
		/*
		$datas = $this->User->find(
			'all', array(
				'conditions' => array('role' => 'user'),
				'fields' => array('User.id', 'User.username', 'User.nick_name', 'User.total_label'),
				'recursive' => 0
			)
		);
		$this->set(compact('datas'));
		*/
		$this->Paginator->settings = array(
			'conditions' => array('role' => 'user'),
			'recursive' => -1,
			'paramType' => 'querystring',
			'limit' => 5,
			'maxLimit' => 100,
			);

		$datas = $this->Paginator->paginate('User');

		$this->set(compact("datas"));

		$labels = $this->User->TabelLabel->find(
			'all', array('recursive' => -1)
		);
		$this->set(compact('labels'));

		$admin = $this->User->find(
			'all', array(
				'conditions' => array('role' => 'admin'),
				'fields' => array('User.id', 'User.username', 'User.nick_name'),
				'limit' => 1,
				'recursive' => -1
			)
		);
		$this->set(compact('admin'));

		$banyakkomen = $this->User->TabelLabel->KomentarStatus->find(
			'count', array(
				'fields' => array('KomentarStatus.id_komentar')
			)
		);
		$this->set(compact('banyakkomen'));

		//read
		$file = new File(WWW_ROOT .  DS .'files'.DS .'setting.txt');
		$json = $file->read(true, 'r');
		$json = json_decode($json);
		$this->set(compact('json'));
	}

	// user only
	public function user($id = null){
		$this->set('title', 'Facebook Crowdsourcing');
		if($id){
			$users = $this->User->find(
				'all', array(
					'conditions' => array('User.id' => $id),
					'fields' => array('User.id', 'User.username', 'User.nick_name', 'User.total_label'),
					'recursive' => -1
				)
			);
			$this->set(compact('users'));

			$user = $users[0]['User']['username'];

			$this->Paginator->settings = array(
			'conditions' => array('TabelLabel.username_pelabel' => $user),
			'recursive' => 2,
			'paramType' => 'querystring',
			'limit' => 5,
			'maxLimit' => 100,
			);

			$datas = $this->Paginator->paginate('TabelLabel');

			$this->set(compact("datas"));

		} else {
			$this->redirect(array('action' => 'index'));
		}
	}

	// admin only
	public function view($id = null){
		$this->set('title', 'Data Pengguna Facebook Crowdsourcing');

		if($id){
			$users = $this->User->find(
				'all', array(
					'conditions' => array('User.id' => $id),
					'fields' => array('User.id', 'User.username', 'User.nick_name', 'User.total_label'),
					'recursive' => -1
				)
			);
			$this->set(compact('users'));

			$user = $users[0]['User']['username'];

			$this->Paginator->settings = array(
			'conditions' => array('TabelLabel.username_pelabel' => $user),
			'recursive' => 2,
			'paramType' => 'querystring',
			'limit' => 5,
			'maxLimit' => 100,
			);

			$datas = $this->Paginator->paginate('TabelLabel');

			$this->set(compact("datas"));

			$chart = $this->TabelLabel->find('all', array(
				'conditions' => array('TabelLabel.username_pelabel' => $user),
				'recursive' => -1,
				'fields' => array('TabelLabel.nama_label')
				)
			);
			$this->set(compact('chart'));
		} else {
			$this->redirect(array('action' => 'index'));
		}
	}

	// admin only
	public function admin($id = null){
		$this->set('title', 'Admin Facebook Crowdsourcing');

		if($id){
			$datas = $this->User->find(
				'all', array('conditions' => array(
					'User.id' => $id)
				)
			);
			$this->set(compact('datas'));
		} 
	}

	public function changesetting($data){
		$this->autoRender = false;
		

		if($this->request->is('ajax'))
		{
			$data = split(" ", $data);
			$datas['price'] = $data[0];
			$datas['n'] = $data[1];
			$json = json_encode($datas);
			
			$file = new File(WWW_ROOT .  DS .'files'.DS .'setting.txt', true);
			$file->write($json);
		}
		else
		{
			$this->redirect(array('action'=>'index'));
		}
		
	}
}