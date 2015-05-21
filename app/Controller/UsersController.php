<?php
App::uses('File', 'Utility');

class UsersController extends AppController {
	public $layout = "layout";
	/*bisa overwrite juga koq*/
	
	public $uses = array('User', 'TabelLabel'); 
	public $components = array('Hybridauth', 'Paginator');
	// admin only
	public function index() {
		$this->set('title','Daftar Pengguna Facebook Crowdsourcing');
		/*
		$datas = $this->User->find(
			'all', array(
				'conditions' => array('role' => 'user'),
				'fields' => array('User.id', 'User.username', 'User.display_name', 'User.total_label'),
				'recursive' => 0
			)
		);
		$this->set(compact('datas'));
		*/
		
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
		
	}

	// user only
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

		}
		
	}

	// admin only
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
	public function useroff($id = null) {
         
        if (!$id) {
            $this->Session->setFlash('Please provide a user id');
            $this->redirect(array('action'=>'index'));
        }
         
        $this->User->id = $id;
        if (!$this->User->exists()) {
            $this->Session->setFlash('Invalid user id provided');
            $this->redirect(array('action'=>'index'));
        }
        if ($this->User->saveField('status', 0)) {
            $this->Session->setFlash(__('User deleted'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('User was not deleted'));
        $this->redirect(array('action' => 'index'));
    }
     
    public function activate($id = null) {
         
        if (!$id) {
            $this->Session->setFlash('Please provide a user id');
            $this->redirect(array('action'=>'index'));
        }
         
        $this->User->id = $id;
        if (!$this->User->exists()) {
            $this->Session->setFlash('Invalid user id provided');
            $this->redirect(array('action'=>'index'));
        }
        if ($this->User->saveField('status', 1)) {
            $this->Session->setFlash(__('User re-activated'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('User was not re-activated'));
        $this->redirect(array('action' => 'index'));
    }
	/*
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
	*/
	public function changesetting($data){
		if($this->Auth->user()['role']=='user')
			$this->redirect(array('action' => 'user'));

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

	public function incrementLabel($id) {
		$this->User->updateAll(
	        array('User.total_label' => 'User.total_label+1'),                    
	        array('User.social_network_id' => $id)
    	);
	}

	//LOGIN SECTION
	//credit to Mifty is bored (miftyisbored.com)
	public function beforeFilter() {
        parent::beforeFilter();

        $this->Auth->allow('login','social_login','social_endpoint');
    }

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
         
         /*
        // if we get the post information, try to authenticate
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                $this->Session->setFlash(__('Welcome, '. $this->Auth->user('username')));
                $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Session->setFlash(__('Invalid username or password'));
            }
        }
        */
    }
 
    public function logout() {
        $this->redirect($this->Auth->logout());
    }

    /* social login functionality */
	public function social_login($provider) {
		if( $this->Hybridauth->connect($provider) ){
			$this->_successfulHybridauth($provider,$this->Hybridauth->user_profile);
        }else{
            // error
			$this->Session->setFlash($this->Hybridauth->error);
			$this->redirect($this->Auth->loginAction);
        }
	}

	public function social_endpoint($provider) {
		$this->Hybridauth->processEndpoint();
	}
	
	private function _successfulHybridauth($provider, $incomingProfile){
		
		// #0 - if not @gmail.com login error
		list($user, $domain) = explode('@', $incomingProfile['User']['email']);

		if ($domain != 'gmail.com') {
			$this->Session->setFlash('Must use @ti.ukdw.ac.id domain for using this app');
			$this->redirect(array('action' => 'login'));
		    //$this->redirect($this->Auth->loginError);
		}
		
		// #1 - check if user already authenticated using this provider before
		$this->User->recursive = -1;
		$existingProfile = $this->User->find('first', array(
			'conditions' => array('social_network_id' => $incomingProfile['User']['social_network_id'])
		));
		
		//#1.1 - check if status is-not-active, can't login
		if($existingProfile['User']['status'] == 0){
			$this->redirect(array('action' => 'login'));
		}

		//debug($existingProfile);
		if ($existingProfile) {
			// #2 - if an existing profile is available, then we set the user as connected and log them in
			
			$user = $this->User->find('first', array(
				'conditions' => array('social_network_id' => $existingProfile['User']['social_network_id'])
			));
			
			//debug($user);
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
	
	private function _doSocialLogin($user, $returning = false) {

		if ($this->Auth->login($user['User'])) {
			if($returning){
				
				$this->Session->setFlash(__('Welcome back, '. $this->Auth->user('display_name')));
			} else {

				$this->Session->setFlash(__('Welcome to our community, '. $this->Auth->user('display_name')));
			}
			$this->redirect($this->Auth->loginRedirect);

			/*
			if($user['User']['role'] == 'admin')
				$this->redirect($this->Auth->adminRedirect);
			else if($user['User']['role'] == 'user')
				$this->redirect($this->Auth->userRedirect);
			*/
			
		} else {
			$this->Session->setFlash(__('Unknown Error could not verify the user: '. $this->Auth->user('display_name')));
		}
	}
}