<?php
App::import('Lib', 'SettingManager');
App::import('Repository', 'KomentarRepository');
App::import('Repository', 'LabelRepository');

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

        $currentUser = $this->Auth->user();
        if($currentUser['role'] == 'user')
            $this->redirect(['action' => 'user']);

        $this->Paginator->settings = [
            'conditions' => ['role' => 'user'],
            'recursive' => -1,
            'paramType' => 'querystring',
            'limit' => 5,
            'maxLimit' => 100,
        ];
        $datas = $this->Paginator->paginate('User');
        $labels = (new LabelRepository())->getAllLabel();
        $banyakkomen = (new KomentarRepository())->count();
        $json = $this->settingGetData();

        $this->set([
            'datas' => $datas,
            'labels' => $labels,
            'admin' => $currentUser,
            'banyakkomen' => $banyakkomen,
            'json' => $json,
        ]);

        //lock hanya muncul jika semua data sudah terlabeli
        $lengkap = (new KomentarRepository())->getUnfinishedLabeledComment();
        if($lengkap);
        else {
            $lockstate = $this->settingGetLock();
            $this->set(['lockstate' => $lockstate]);
        }
    }

    // priviledge: user
    // method for user see their history for labeling a comment, and they can edit it if available
    public function user($id = null)
    {
        $this->set('title', 'Facebook Crowdsourcing');

        $currentUser = $this->Auth->user();
        if($currentUser){
            $this->Paginator->settings = $this->getPaginatorSettingForAllLabel($currentUser['email']);
            $datas = $this->Paginator->paginate('Label');

            $cannotLabellingAgain = (new LabelRepository())->checkLabellingPermissionForAUser($currentUser['social_network_id']);

            $isExistCommentWithLabel = $this->canLabel();
            $lockstate = $this->settingGetLock();

            $this->set([
                'currentUser' => $currentUser,
                'datas' => $datas,
                'isExistCommentWithLabel' => $isExistCommentWithLabel,
                'cannotLabellingAgain' => $cannotLabellingAgain,
                'lockstate' => $lockstate,
            ]);
        }
    }

    private function settingGetData()
    {
        $settingObject = SettingManager::getSettingObject('setting.txt');
        return $settingObject->getData();
    }

    private function settingGetN()
    {
        $settingObject = SettingManager::getSettingObject('setting.txt');
        return $settingObject->getN();
    }

    private function settingGetLock()
    {
        $settingObject = SettingManager::getSettingObject('setting.txt');
        return $settingObject->getLock();
    }

    // method for set lock state
    public function setLock($value)
    {
        $data['lock'] = $value;
        $settingObject = SettingManager::getSettingObject('setting.txt');
        return $settingObject->setLock($data);

        //saat di lock, maka update label akhir sebuah komentar
        if($value == 'true')
            (new KomentarRepository())->determineFinalLabelForEveryComment();
        else
            $this->redirect(['action'=>'index']);
    }


    // method for check, is it still available to give a label
    private function canLabel()
    {
        $maxlabel = $this->settingGetN();

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
        if($this->Auth->user()['role'] == 'user')
            $this->redirect(['action' => 'user']);

        $this->set('title', 'Data Pengguna Facebook Crowdsourcing');

        if($id){
            $users = $this->User->find('all', [
                'conditions' => ['User.id' => $id],
                'fields' => ['User.id', 'User.email', 'User.display_name', 'User.total_label', 'User.picture'],
                'recursive' => -1
            ]);

            $user = $users[0]['User']['email'];
            $this->Paginator->settings = $this->getPaginatorSettingForAllLabel($user);
            $datas = $this->Paginator->paginate('Label');

            $chart = (new LabelRepository())->getAllLabelRelatedToUser($user);

            $this->set([
                'users' => $users,
                'datas' => $datas,
                'chart' => $chart,
            ]);
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
            $lock = $this->settingGetLock();
            $cetak = $this->request->data;
            $datas['price'] = $cetak['User']['harga'];
            $datas['n'] = $cetak['User']['label'];
            $datas['lock'] = $lock;

            $maxLabelCount = (new KomentarRepository())->getMaxLabelInAComment();

            if($maxLabelCount[0][0]['maxLabelCount'] < $cetak['User']['label']){

                (new KomentarRepository())->setStatusToWhereJmlLabel('belum', $this->settingGetN());
                $datas['lock'] = 'false';
            } else if($maxLabelCount[0][0]['maxLabelCount'] == $cetak['User']['label']){

                (new KomentarRepository())->setStatusToWhereJmlLabel('lengkap', $this->settingGetN());
                $datas['n'] = $maxLabelCount[0][0]['maxLabelCount'];
            } else{

                $datas['n'] = $maxLabelCount[0][0]['maxLabelCount'];
            }

            $settingObject = SettingManager::getSettingObject('setting.txt');
            $settingObject->setData($datas);
            $this->redirect(['action'=>'index']);
        }
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
        $this->set('title', 'Crowd Sourcing Facebook');

        //if already logged-in, redirect
        if($this->Session->check('Auth.User')) {
            $this->redirect(['action' => 'index']);
        }

        $labelCount = (new LabelRepository())->count();
        $commentCount = (new KomentarRepository())->count();
        $json = $this->settingGetData();

        $this->set([
            'labelCount' => $labelCount,
            'commentCount' => $commentCount,
            'json' => $json
        ]);
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


    private function getPaginatorSettingForAllLabel($userEmail)
    {
        return [
            'conditions' => ['Label.username_pelabel' => $userEmail],
            'recursive' => 1,
            'paramType' => 'querystring',
            'joins' => [
                [
                    'table' => 'statuses',
                    'alias' => 'Status',
                    'type' => 'LEFT',
                    'conditions' => ['Label.id_status = Status.id_status']
                ]
            ],
            'fields' => [
                'Label.id_label', 'Label.id_status', 'Label.id_komen', 'Label.username_pelabel', 'Label.waktu_melabel', 'Label.nama_label',
                'Komentar.id_komentar', 'Komentar.komentar',
                'Status.id_status', 'Status.teks_status'
            ],
            'limit' => 5,
            'maxLimit' => 100,
        ];
    }
}
