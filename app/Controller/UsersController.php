<?php
App::uses('File', 'Utility');

class UsersController extends AppController {
	public $layout = "layout";
	
	public $uses = array('User', 'TabelLabel'); 
	public $components = array('Hybridauth', 'Paginator');
	
	// priviledge: admin
	// method for viewing all user that uses this app and the setting configuration
	public function index() {
		$this->set('title','Daftar Pengguna Facebook Crowdsourcing');

		if($this->Auth->user()['role']=='user')
			$this->redirect(array('action' => 'user'));

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
				'fields' => array('User.id', 'User.email', 'User.display_name'),
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



		//lock hanya muncul jika semua data sudah terlabeli
		$lengkap = $this->User->TabelLabel->KomentarStatus->find('all', array(
			'conditions' => array('KomentarStatus.status' => 'belum'),
			'recursive' => -1
			)
		);
		
		if($lengkap);
		else{
			$lockstate = $this->getLock();
			$this->set(compact('lockstate'));
		}
	}

	// priviledge: user 
	// method for user see their history for labeling a comment, and they can edit it if available
	public function user($id = null){
		$this->set('title', 'Facebook Crowdsourcing');

		$id = $this->Auth->user()['social_network_id'];
		if($id){
			$users = $this->User->find(
				'all', array(
					'conditions' => array('User.social_network_id' => $id),
					'fields' => array('User.id', 'User.email', 'User.display_name', 'User.total_label'),
					'recursive' => -1
				)
			);
			$this->set(compact('users'));

			$user = $users[0]['User']['email'];

			$this->Paginator->settings = array(
			'conditions' => array('TabelLabel.username_pelabel' => $user),
			'recursive' => 2,
			'paramType' => 'querystring',
			'limit' => 5,
			'maxLimit' => 100,
			);

			$datas = $this->Paginator->paginate('TabelLabel');

			$this->set(compact("datas"));

			//hitung total komentar
			$komentars = $this->User->TabelLabel->KomentarStatus->find('count');

			//lihat user sudah melabeli berapa komentar
			$labelsekarang = $this->User->TabelLabel->KomentarStatus->countusers($id);
			//buat permissionnya
			$tambahlabel = false;
			if($labelsekarang[0][0]['jumlah'] == $komentars)
				$tambahlabel = true;
			
			$this->set(compact('tambahlabel'));

			$canlabel = $this->canLabel();
			$this->set(compact('canlabel'));
			
			$lockstate = $this->getLock();
			$this->set(compact('lockstate'));
		}
		
	}

	// method for get lock state in configuration file
	private function getLock(){
		//baca maksimum label per komentar
		$file = new File(WWW_ROOT .  DS .'files'.DS .'setting.txt');
		$json = $file->read(true, 'r');
		$json = json_decode($json);
		return $json->lock;
	}

	// admin only
	// method for set lock state 
	public function setLock($value){
		//baca maksimum label per komentar
		$file = new File(WWW_ROOT .  DS .'files'.DS .'setting.txt');
		$json = $file->read(true, 'r');
		$json = json_decode($json);

		$datas['price'] = $json->price;
		$datas['n'] = $json->n;
		$datas['lock'] = $value;
		$json = json_encode($datas);

		$file = new File(WWW_ROOT .  DS .'files'.DS .'setting.txt', true);
		$file->write($json);

		//saat di lock, maka update label akhir sebuah komentar
		if($value == 'true')
			$this->redirect(array('controller' => 'Statuses','action'=>'calculate'));
		else 
			$this->redirect(array('action'=>'index'));
	}

	// priviledge: user 
	// method for get number of max label in configuration file
	private function getN(){
		//baca maksimum label per komentar
		$file = new File(WWW_ROOT .  DS .'files'.DS .'setting.txt');
		$json = $file->read(true, 'r');
		$json = json_decode($json);
		return $json->n;
	}

	// method for check, is it still available to give a label
	private function canLabel(){

		$maxlabel = $this->getN();

		$komentarlabel = $this->User->TabelLabel->KomentarStatus->getrandom($this->Auth->user()['email'], $maxlabel);
		if($komentarlabel)
			return true;
		else 
			return false;
	}


	// priviledge: admin 
	// method for view comments that have been labelled by a user 
	public function view($id = null){
		if($this->Auth->user()['role']=='user')
			$this->redirect(array('action' => 'user'));

		$this->set('title', 'Data Pengguna Facebook Crowdsourcing');

		if($id){
			$users = $this->User->find(
				'all', array(
					'conditions' => array('User.id' => $id),
					'fields' => array('User.id', 'User.email', 'User.display_name', 'User.total_label', 'User.picture'),
					'recursive' => -1
				)
			);
			$this->set(compact('users'));

			$user = $users[0]['User']['email'];

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

	// priviledge: admin 
	// method for deactivate a user
	public function useroff($id = null) {
         
        if (!$id) {
            $this->Session->setFlash('Please provide a user id', 'customflash', array('class' => 'warning'));
            $this->redirect(array('action'=>'index'));
        }
         
        $this->User->id = $id;
        if (!$this->User->exists()) {
            $this->Session->setFlash('Invalid user id provided', 'customflash', array('class' => 'warning'));
            $this->redirect(array('action'=>'index'));
        }
        if ($this->User->saveField('status', 0)) {
            $this->Session->setFlash(__('User telah dide-activate'),'customflash', array('class' => 'danger'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('User was not deleted'), 'customflash', array('class' => 'info'));
        $this->redirect(array('action' => 'index'));
    }
    
    // priviledge: admin 
	// method for reactivate a user
    public function activate($id = null) {
         
        if (!$id) {
            $this->Session->setFlash('Please provide a user id', 'customflash', array('class' => 'warning'));
            $this->redirect(array('action'=>'index'));
        }
         
        $this->User->id = $id;
        if (!$this->User->exists()) {
            $this->Session->setFlash('Invalid user id provided', 'customflash', array('class' => 'warning'));
            $this->redirect(array('action'=>'index'));
        }
        if ($this->User->saveField('status', 1)) {
            $this->Session->setFlash(__('User telah dire-activated'),'customflash', array('class' => 'success'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('User gagal dire-activated'),'customflash', array('class' => 'info'));
        $this->redirect(array('action' => 'index'));
    }
	
	
	// priviledge: admin 
	// method for admin to change configuration file
	public function changesetting(){
		if($this->request->is('post')){
			$lock = $this->getLock();
			$cetak = $this->request->data;
			$datas['price'] = $cetak['User']['harga'];
			$datas['n'] = $cetak['User']['label'];
			$datas['lock'] = $lock;

			$maxnow = $this->User->TabelLabel->KomentarStatus->getMaxJmlLabel();
		
			if($maxnow[0][0]['maxi'] < $cetak['User']['label']){
				$this->User->TabelLabel->KomentarStatus->updatestatus('belum', $this->getN());
			} else if($maxnow[0][0]['maxi'] == $cetak['User']['label']){
				
				$datas['n'] = $maxnow[0][0]['maxi'];
				$this->User->TabelLabel->KomentarStatus->updatestatus('lengkap', $maxnow[0][0]['maxi']);
			} else{
				$datas['n'] = $maxnow[0][0]['maxi'];
				echo "no ".$datas['n'];
			}
				

			$json = json_encode($datas);

			$file = new File(WWW_ROOT .  DS .'files'.DS .'setting.txt', true);
			$file->write($json);
			$this->redirect(array('action'=>'index'));
		}	
	}



	// priviledge: user 
	// method for increment total_label that has been made by a user
	public function incrementLabel($id) {
		$this->User->updateAll(
	        array('User.total_label' => 'User.total_label+1'),                    
	        array('User.social_network_id' => $id)
    	);
	}

	// LOGIN SECTION
	// credit to Mifty is bored (miftyisbored.com)
	
	// method to filter allowed function before login is being made
	public function beforeFilter() {
        parent::beforeFilter();

        $this->Auth->allow('login','social_login','social_endpoint');
    }

    // priviledge: all
	// method for provide login page 
	public function login() {
        //if already logged-in, redirect
        if($this->Session->check('Auth.User')){
            $this->redirect(array('action' => 'index'));     
        }

        $this->set('title', 'Crowd Sourcing Facebook');

	    App::import('Controller', 'Statuses');
	    $StatusesController = new StatusesController;

	    $labels = $StatusesController->countlabel();
	    $this->set(compact('labels'));

	    $comments = $StatusesController->countkomentar();
	    $this->set(compact('comments'));

	    $StatusesController = null;

	    //read
		$file = new File(WWW_ROOT .  DS .'files'.DS .'setting.txt');
		$json = $file->read(true, 'r');
		$json = json_decode($json);
		$this->set(compact('json'));
    }
 	
	// method for process logout
    public function logout() {
        $this->redirect($this->Auth->logout());
    }

    /* social login functionality */
    // method to handle login from outside service
    // if success, it will redirect to _successfulHybridauth
    // else it will give error message
	public function social_login($provider) {
		if( $this->Hybridauth->connect($provider) ){
			$this->_successfulHybridauth($provider,$this->Hybridauth->user_profile);
        }else{
            // error
			$this->Session->setFlash($this->Hybridauth->error);
			$this->redirect($this->Auth->loginAction);
        }
	}

	// method that responsible for all interactions with the social network 
	// and that it is the URL that the social network will call when it needs information from our appliactoin
	public function social_endpoint($provider) {
		$this->Hybridauth->processEndpoint();
	}
	
	// method that complete the actual login process
	// and also informs the CakePHP Auth component to let the user in
	private function _successfulHybridauth($provider, $incomingProfile){
		
		// #0 - if not @gmail.com login error
		list($user, $domain) = explode('@', $incomingProfile['User']['email']);
		
		if ($domain != 'gmail.com') {
			$this->Session->setFlash('Must use @ti.ukdw.ac.id domain for using this app', 'customflash', array('class' => 'danger'));
			$this->redirect(array('action' => 'login'));
		    //$this->redirect($this->Auth->loginError);
		}
		
		// #1 - check if user already authenticated using this provider before
		$this->User->recursive = -1;
		$existingProfile = $this->User->find('first', array(
			'conditions' => array('social_network_id' => $incomingProfile['User']['social_network_id'])
		));
		
		if ($existingProfile) {
			// #2 - if an existing profile is available, then we set the user as connected and log them in
			
			$user = $this->User->find('first', array(
				'conditions' => array('social_network_id' => $existingProfile['User']['social_network_id'])
			));
			

			//#2.1 - check if status is-not-active, can't login
			if($existingProfile['User']['status'] == 0){
				$this->Session->setFlash(__('Maaf, akun anda sudah di non-aktifkan'), 'customflash', array('class' => 'danger'));
				$this->redirect(array('action' => 'login'));
			}

			$this->_doSocialLogin($user,true);
		} else {
			// New profile.
			if ($this->Auth->loggedIn()) {
				// user is already logged-in , attach profile to logged in user.
				$this->User->save($incomingProfile);
				
				//$this->Session->setFlash('Your ' . $incomingProfile['User']['social_network_name'] . ' account is now linked to your account.');
				$this->redirect($this->Auth->redirectUrl());

			} else {
				// no-one logged and no profile, must be a registration.
				$this->User->save($incomingProfile);

				// log in with the newly created user
				$this->_doSocialLogin($incomingProfile);
			}
			
		}	
	}
	
	// method that tells CakePHP’s Auth component that the user has been authenticated
	private function _doSocialLogin($user, $returning = false) {

		if ($this->Auth->login($user['User'])) {
			if($returning){
				
				$this->Session->setFlash(__('Selamat datang, '. $this->Auth->user('display_name')), 'customflash', array('class' => 'info'));
			} else {

				$this->Session->setFlash(__('Selamat datang di Crowd Sourcing Facebook, '. $this->Auth->user('display_name')), 'customflash', array('class' => 'info'));
			}
			$this->redirect($this->Auth->loginRedirect);			
		} else {
			$this->Session->setFlash(__('Unknown Error, user tidak dapat diverifikasi: '. $this->Auth->user('display_name')), 'customflash', array('class' => 'danger'));
		}
	}
}