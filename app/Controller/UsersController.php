<?php
App::uses('File', 'Utility');

class UsersController extends AppController
{
    public $layout = "layout";

    public $uses = ['User', 'Label'];
    public $components = ['Hybridauth', 'Paginator'];

    // priviledge: admin
    // method for viewing all user that uses this app and the setting configuration
    public function index()
    {
        $this->set('title','Daftar Pengguna Facebook Crowdsourcing');

        if($this->Auth->user()['role']=='user')
            $this->redirect(['action' => 'user']);

        $this->Paginator->settings = [
            'conditions' => ['role' => 'user'],
            'recursive' => -1,
            'paramType' => 'querystring',
            'limit' => 5,
            'maxLimit' => 100,
        ];

        $datas = $this->Paginator->paginate('User');

        $this->set(['datas' => $datas]);

        $labels = $this->User->Label->find('all', [
            'recursive' => -1]);

        $this->set(['labels' => $labels]);

        $admin = $this->User->find('all', [
            'conditions' => ['role' => 'admin'],
            'fields' => ['User.id', 'User.email', 'User.display_name'],
            'limit' => 1,
            'recursive' => -1
        ]);
        $this->set(['admin' => $admin]);

        $banyakkomen = $this->User->Label->Komentar->find('count', [
            'fields' => ['Komentar.id_komentar']
        ]);
        $this->set(['banyakkomen' => $banyakkomen]);

        //read
        $file = new File(WWW_ROOT .  DS .'files'.DS .'setting.txt');
        $json = $file->read(true, 'r');
        $json = json_decode($json);
        $this->set(['json' => $json]);

        //lock hanya muncul jika semua data sudah terlabeli
        $lengkap = $this->User->Label->Komentar->find('all', [
            'conditions' => ['Komentar.status' => 'belum'],
            'recursive' => -1
        ]);

        if($lengkap);
        else {
            $lockstate = $this->getLock();
            $this->set(['lockstate' => $lockstate]);
        }
    }

    // priviledge: user
    // method for user see their history for labeling a comment, and they can edit it if available
    public function user($id = null)
    {
        $this->set('title', 'Facebook Crowdsourcing');

        $id = $this->Auth->user()['social_network_id'];
        if($id){
            $users = $this->User->find('all', [
                'conditions' => ['User.social_network_id' => $id],
                'fields' => ['User.id', 'User.email', 'User.display_name', 'User.total_label'],
                'recursive' => -1
            ]);
            $this->set(['users' => $users]);

            $user = $users[0]['User']['email'];

            $this->Paginator->settings = [
                'conditions' => ['Label.username_pelabel' => $user],
                'recursive' => 2,
                'paramType' => 'querystring',
                'limit' => 5,
                'maxLimit' => 100,
            ];

            $datas = $this->Paginator->paginate('Label');
            $this->set(['datas' => $datas]);

            //hitung total komentar
            $komentars = $this->User->Label->Komentar->find('count');

            //lihat user sudah melabeli berapa komentar
            $labelsekarang = $this->User->Label->Komentar->countUsers($id);
            //buat permissionnya
            $tambahlabel = false;
            if($labelsekarang[0][0]['jumlah'] == $komentars)
                $tambahlabel = true;

            $this->set(['tambahlabel' => $tambahlabel]);

            $canlabel = $this->canLabel();
            $this->set(['canlabel' => $canlabel]);

            $lockstate = $this->getLock();
            $this->set(['lockstate' => $lockstate]);
        }
    }

    // method for get lock state in configuration file
    private function getLock()
    {
        //baca maksimum label per komentar
        $file = new File(WWW_ROOT .  DS .'files'.DS .'setting.txt');
        $json = $file->read(true, 'r');
        $json = json_decode($json);
        return $json->lock;
    }

    // admin only
    // method for set lock state
    public function setLock($value)
    {
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
            $this->redirect(['controller' => 'Statuses','action'=>'calculate']);
        else
            $this->redirect(['action'=>'index']);
    }

    // priviledge: user
    // method for get number of max label in configuration file
    private function getN()
    {
        //baca maksimum label per komentar
        $file = new File(WWW_ROOT .  DS .'files'.DS .'setting.txt');
        $json = $file->read(true, 'r');
        $json = json_decode($json);
        return $json->n;
    }

    // method for check, is it still available to give a label
    private function canLabel()
    {
        $maxlabel = $this->getN();

        $komentarlabel = $this->User->Label->Komentar->getRandom($this->Auth->user()['email'], $maxlabel);
        if($komentarlabel)
            return true;
        else
            return false;
    }


    // priviledge: admin
    // method for view comments that have been labelled by a user
    public function view($id = null)
    {
        if($this->Auth->user()['role']=='user')
            $this->redirect(['action' => 'user']);

        $this->set('title', 'Data Pengguna Facebook Crowdsourcing');

        if($id){
            $users = $this->User->find('all', [
                'conditions' => ['User.id' => $id],
                'fields' => ['User.id', 'User.email', 'User.display_name', 'User.total_label', 'User.picture'],
                'recursive' => -1
            ]);
            $this->set(['users' => $users]);

            $user = $users[0]['User']['email'];

            $this->Paginator->settings = [
                'conditions' => ['Label.username_pelabel' => $user],
                'recursive' => 2,
                'paramType' => 'querystring',
                'limit' => 5,
                'maxLimit' => 100,
            ];

            $datas = $this->Paginator->paginate('Label');
            $this->set(['datas' => $datas]);

            $chart = $this->Label->find('all', [
                'conditions' => ['Label.username_pelabel' => $user],
                'recursive' => -1,
                'fields' => ['Label.nama_label']
            ]);
            $this->set(['chart' => $chart]);
        } else {
            $this->redirect(['action' => 'index']);
        }
    }

    // priviledge: admin
    // method for deactivate a user
    public function useroff($id = null)
    {
        if (!$id) {
            $this->Session->setFlash('Please provide a user id', 'customflash', ['class' => 'warning']);
            $this->redirect(['action'=>'index']);
        }

        $this->User->id = $id;
        if (!$this->User->exists()) {
            $this->Session->setFlash('Invalid user id provided', 'customflash', ['class' => 'warning']);
            $this->redirect(['action'=>'index']);
        }
        if ($this->User->saveField('status', 0)) {
            $this->Session->setFlash(__('User telah dide-activate'),'customflash', ['class' => 'danger']);
            $this->redirect(['action' => 'index']);
        }
        $this->Session->setFlash(__('User was not deleted'), 'customflash', ['class' => 'info']);
        $this->redirect(['action' => 'index']);
    }

    // priviledge: admin
    // method for reactivate a user
    public function activate($id = null)
    {
        if (!$id) {
            $this->Session->setFlash('Please provide a user id', 'customflash', ['class' => 'warning']);
            $this->redirect(['action'=>'index']);
        }

        $this->User->id = $id;
        if (!$this->User->exists()) {
            $this->Session->setFlash('Invalid user id provided', 'customflash', ['class' => 'warning']);
            $this->redirect(['action'=>'index']);
        }
        if ($this->User->saveField('status', 1)) {
            $this->Session->setFlash(__('User telah dire-activated'),'customflash', ['class' => 'success']);
            $this->redirect(['action' => 'index']);
        }
        $this->Session->setFlash(__('User gagal dire-activated'),'customflash', ['class' => 'info']);
        $this->redirect(['action' => 'index']);
    }


    // priviledge: admin
    // method for admin to change configuration file
    public function changesetting()
    {
        if($this->request->is('post')) {
            $lock = $this->getLock();
            $cetak = $this->request->data;
            $datas['price'] = $cetak['User']['harga'];
            $datas['n'] = $cetak['User']['label'];
            $datas['lock'] = $lock;

            $maxnow = $this->User->Label->Komentar->getMaxJmlLabel();

            if($maxnow[0][0]['maxi'] < $cetak['User']['label']){

                $this->User->Label->Komentar->updateStatus('belum', $this->getN());
                $datas['lock'] = 'false';
            } else if($maxnow[0][0]['maxi'] == $cetak['User']['label']){

                $datas['n'] = $maxnow[0][0]['maxi'];
                $this->User->Label->Komentar->updateStatus('lengkap', $maxnow[0][0]['maxi']);
            } else{

                $datas['n'] = $maxnow[0][0]['maxi'];
            }

            $json = json_encode($datas);

            $file = new File(WWW_ROOT .  DS . 'files' . DS .'setting.txt', true);
            $file->write($json);
            $this->redirect(['action'=>'index']);
        }
    }

    // priviledge: user
    // method for increment total_label that has been made by a user
    public function incrementLabel($id)
    {
        $this->User->updateAll(
            ['User.total_label' => 'User.total_label+1'],
            ['User.social_network_id' => $id]
        );
    }

    // LOGIN SECTION
    // credit to Mifty is bored (miftyisbored.com)

    // method to filter allowed function before login is being made
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allow('login','social_login','social_endpoint');
    }

    // priviledge: all
    // method for provide login page
    public function login()
    {
        //if already logged-in, redirect
        if($this->Session->check('Auth.User')) {
            $this->redirect(['action' => 'index']);
        }

        $this->set('title', 'Crowd Sourcing Facebook');

        App::import('Controller', 'Statuses');
        $StatusesController = new StatusesController;

        $labels = $StatusesController->countlabel();
        $this->set(['labels' => $labels]);

        $comments = $StatusesController->countkomentar();
        $this->set(['comments' => $comments]);

        $StatusesController = null;

        //read
        $file = new File(WWW_ROOT .  DS .'files'.DS .'setting.txt');
        $json = $file->read(true, 'r');
        $json = json_decode($json);
        $this->set(['json' => $json]);
    }

    // method for process logout
    public function logout()
    {
        $this->redirect($this->Auth->logout());
    }

    /* social login functionality */
    // method to handle login from outside service
    // if success, it will redirect to _successfulHybridauth
    // else it will give error message
    public function social_login($provider)
    {
        if ($this->Hybridauth->connect($provider)) {
            $this->_successfulHybridauth($provider,$this->Hybridauth->user_profile);
        } else {
            // error
            $this->Session->setFlash($this->Hybridauth->error);
            $this->redirect($this->Auth->loginAction);
        }
    }

    // method that responsible for all interactions with the social network
    // and that it is the URL that the social network will call when it needs information from our appliactoin
    public function social_endpoint($provider)
    {
        $this->Hybridauth->processEndpoint();
    }

    // method that complete the actual login process
    // and also informs the CakePHP Auth component to let the user in
    private function _successfulHybridauth($provider, $incomingProfile)
    {
        // #0 - if not @gmail.com login error
        list($user, $domain) = explode('@', $incomingProfile['User']['email']);

        // if ($domain != 'gmail.com') {
        // 	$this->Session->setFlash('Must use @ti.ukdw.ac.id domain for using this app', 'customflash', ['class' => 'danger'));
        // 	$this->redirect(['action' => 'login'));
        //     //$this->redirect($this->Auth->loginError);
        // }

        // #1 - check if user already authenticated using this provider before
        $this->User->recursive = -1;
        $existingProfile = $this->User->find('first', [
            'conditions' => ['social_network_id' => $incomingProfile['User']['social_network_id']]
        ]);

        if ($existingProfile) {
            // #2 - if an existing profile is available, then we set the user as connected and log them in

            $user = $this->User->find('first', [
                'conditions' => ['social_network_id' => $existingProfile['User']['social_network_id']]
            ]);


            //#2.1 - check if status is-not-active, can't login
            if($existingProfile['User']['status'] == 0) {
                $this->Session->setFlash(__('Maaf, akun anda sudah di non-aktifkan'), 'customflash', ['class' => 'danger']);
                $this->redirect(['action' => 'login']);
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
    private function _doSocialLogin($user, $returning = false)
    {
        if ($this->Auth->login($user['User'])) {
            if($returning){

                $this->Session->setFlash(__('Selamat datang, '. $this->Auth->user('display_name')), 'customflash', ['class' => 'info']);
            } else {

                $this->Session->setFlash(__('Selamat datang di Crowd Sourcing Facebook, '. $this->Auth->user('display_name')), 'customflash', ['class' => 'info']);
            }
            $this->redirect($this->Auth->loginRedirect);
        } else {
            $this->Session->setFlash(__('Unknown Error, user tidak dapat diverifikasi: '. $this->Auth->user('display_name')), 'customflash', ['class' => 'danger']);
        }
    }
}
