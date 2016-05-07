<?php
App::uses('File', 'Utility');

class StatusesController extends AppController
{
    public $uses = ['Status', 'Komentar'];
    public $layout = 'layout';
    public $helpers = ['Html', 'Form', 'Csv'];
    public $components = ['Paginator'];

    // priviledge: admin only
    // method for showing all status
    public function index()
    {
        if($this->Auth->user()['role']=='user')
            $this->redirect(['controller' => 'users', 'action' => 'user']);

        $this->set('title','Daftar Status Facebook');

        $this->Paginator->settings = [
            'limit' => 5,
            'recursive' => 0,
            'paramType' => 'querystring',
            'limit' => 5,
            'maxLimit' => 100,
        ];

        $datas = $this->Paginator->paginate('Status');

        //lock hanya muncul jika semua data sudah terlabeli
        $lengkap = $this->Status->Komentar->find('all', [
                'conditions' => ['Komentar.status' => 'belum'],
                'recursive' => -1
            ]
        );

        if($lengkap);
        else {
            $lockstate = $this->getLock();
            $this->set(['lockstate' => $lockstate]);
        }

        $this->set(['datas' => $datas]);
    }

    // priviledge: admin only
    // method for showing all a single status and all comment related to it
    public function view($id = null)
    {
        if($this->Auth->user()['role']=='user')
            $this->redirect(['controller' => 'users', 'action' => 'user']);

        if($id){
            $this->set('title','Status Facebook');

            $status = $this->Status->find('all', [
                'conditions' => ['Status.id_status' => $id],
                'recursive' => 0
            ]);
            $this->set(['status' => $status]);

            $this->Paginator->settings = [
                'conditions' => ['Komentar.id_status' => $id],
                'recursive' => 1,
                'paramType' => 'querystring',
                'limit' => 5,
                'maxLimit' => 100,
            ];

            $komentars = $this->Paginator->paginate('Komentar');
            $this->set(['komentars' => $komentars]);

            $maxlabel = $this->getN();
            $this->set(['maxlabel' => $maxlabel]);
        } else {
            $this->redirect(['action' => 'index']);
        }
    }

    //JSON -------------------------------------------------------
    public function detail($id)
    {

        $this->autoRender = false;
        if($this->request->is('ajax')) {
            $datas = $this->Status->Komentar->find('all', [
                'conditions' => ['Komentar.id_komentar' => $id],
                'contain' => ['Label.id_label', 'Label.username_pelabel', 'Label.nama_label']
            ]);

            if($datas) {
                echo json_encode($datas);
            } else {
                echo 'no';
            }
        } else {
            $this->redirect(['action'=>'index']);
        }
    }


    // priviledge: this class
    // method for getting a random comment to be labelled by user
    private function randkomentar($id)
    {
        //baca maksimum label per komentar
        $maxlabel = $this->getN();
        //ambil username user
        $users = $this->Komentar->Label->User->find('first', [
            'conditions' => ['User.social_network_id' => $id],
            'fields' => ['User.email'],
            'recursive' => -1
        ]);
        $users = $users['User']['email'];

        $datas = $this->Komentar->getRandom($users, $maxlabel);
        return $datas;
    }

    // priviledge: user
    // method for user can labeling a comment or not
    public function labeling($id = null, $user = null, $id_komen = null, $id_status = null, $label = null)
    {
        if($this->Auth->user()['role']=='admin')
            $this->redirect(['controller' => 'users', 'action' => 'index']);

        //hitung total komentar
        $komentars = $this->Komentar->find('count');

        //lihat user sudah melabeli berapa komentar
        $labelsekarang = $this->Komentar->countUsers($id);
        //buat permissionnya
        $tambahlabel = false;
        if($labelsekarang[0][0]['jumlah'] == $komentars)
            $tambahlabel = true;

        if($tambahlabel == true)
            $this->redirect(['controller' => 'users', 'action' => 'user']);

        $id = $this->Auth->user()['social_network_id'];

        if($this->request->is('post')) {

            //cek jml_label di komen, apa sudah sesuai N ? kl sudah, set flash gagal
            if($this->ceklabelkomennow($id_komen))
                $this->Session->setFlash('Komentar "$id_komen" gagal dilabeli', 'customflash', ['class' => 'warning']);
            else{
                //kalau belum jalankan update
                $data['Label']['id_status'] = $id_status;
                $data['Label']['id_komen'] = $id_komen;
                $data['Label']['username_pelabel'] = $user;
                $data['Label']['nama_label'] = $label;

                $this->Komentar->Label->save($data);

                App::import('Controller', 'Users');
                $UsersController = new UsersController;
                $UsersController->incrementLabel($id);

                $this->inckomenlabel($id_komen);
                $this->Session->setFlash('Komentar "$id_komen" berhasil dilabeli', 'customflash', ['class' => 'success']);
            }
            $this->redirect(['action' => 'labeling', $id]);
        } else if ($id) {
            $this->set('title', 'Pelabelan Komentar');
            $datas = $this->randkomentar($id);
            if($datas)
                $this->set(['datas' => $datas]);
            else
                $this->redirect(['controller' => 'users', 'action' => 'user', $id]);
            //$log = $this->Status->getDataSource()->getLog(false, false);
        }
    }

    // priviledge: user
    // method for user edit label that has given to a comment
    public function edit($id_komentar = null, $id_label = null)
    {
        if($this->Auth->user()['role']=='admin')
            $this->redirect(['controller' => 'users', 'action' => 'index']);

        $this->set('title', 'Edit Label Komentar');
        if($this->getLock() == 'true')
            $this->redirect(['controller' => 'users', 'action' => 'user']);

        if($this->request->is('post')){

            $data = ['nama_label' => $id_label];
            $this->Komentar->Label->id = $id_komentar;
            $this->Komentar->Label->save($data);

            return $this->redirect(['controller' => 'users', 'action' => 'user']);
        } else if($id_komentar != null && $id_label != null) {
            $this->set('title','Status Facebook');

            $datas = $this->Status->Komentar->find('all', [
                'conditions' => ['Komentar.id_komentar' => $id_komentar],
                'recursive' => 0
            ]);
            $this->set(['datas' => $datas]);

            $labels = $this->Komentar->Label->findByIdLabel($id_label);
            $this->set(['labels' => $labels]);
        } else {
            $this->redirect(['action' => 'index']);
        }
    }

    // method for export all comment and it's status
    public function expKomentar()
    {
        $this->layout = null;
        $datas = $this->Status->Komentar->getKomen();
        $this->set(['datas' => $datas]);

        $this->autoLayout = false;
    }

    // method for export single status, and all comment that related to it
    public function expStatus($id)
    {
        $this->layout = null;
        $datas = $this->Status->Komentar->getStatus($id);
        $this->set(['datas' => $datas]);

        $this->autoLayout = false;
    }

    // method for count the final label that will be given to a comment
    public function calculate()
    {
        $datas = $this->Status->Komentar->find('all');
        $this->set(['datas' => $datas]);

        foreach($datas as $data){
            $value = 0;
            foreach ($data['Label'] as $label) {
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

            $label = ['id_komentar' => $data['Komentar']['id_komentar'], 'label' => $value];
            $this->Status->Komentar->save($label);
        }
        $this->redirect(['controller' => 'users', 'action' => 'index']);
    }

    // method for get total number of labels that have been made by all user
    public function countlabel()
    {
        return $this->Status->Komentar->Label->find(
            'count', ['fields' => ['Label.id_label']]
        );
    }

    // method for count how many comments that exist in db
    public function countkomentar()
    {
        return $this->Status->Komentar->find(
            'count', ['fields' => ['Komentar.id_komentar']]
        );
    }


    // method for increment number of label that has been given at certain comment.
    // this method also update the status of a comment, if labels count is the same as configuration file
    private function inckomenlabel($id)
    {
        $this->Status->Komentar->updateAll(
            ['Komentar.jml_label' => 'Komentar.jml_label+1'],
            ['Komentar.id_komentar' => $id]
        );

        $jmllabel = $this->Status->Komentar->find('first', [
            'conditions' => ['Komentar.id_komentar' => $id],
            'fields' => 'Komentar.jml_label',
            'recursive' => -1
        ]);

        //kalau sudah sama dengan N, update statusnya
        if($jmllabel['Komentar']['jml_label'] == $this->getN()) {
            $data = ['id_komentar' => $id, 'status' => 'lengkap'];
            $this->Status->Komentar->save($data);
        }
    }

    // method for check the label of a comment
    // return true if the status = 'lengkap', else will be return false
    private function ceklabelkomennow($id)
    {
        $statuslabel = $this->Status->Komentar->find('first', [
            'conditions' => ['Komentar.id_komentar' => $id],
            'fields' => 'Komentar.status',
            'recursive' => -1
        ]);

        if($statuslabel['Komentar']['status'] == 'lengkap')
            return true;
        else
            return false;
    }

    // method for get the max number of label for a comment
    private function getN()
    {
        $file = new File(WWW_ROOT .  DS .'files'.DS .'setting.txt');
        $json = $file->read(true, 'r');
        $json = json_decode($json);
        return $json->n;
    }

    // method for get the current configuration for locking state
    private function getLock()
    {
        //baca maksimum label per komentar
        $file = new File(WWW_ROOT .  DS .'files'.DS .'setting.txt');
        $json = $file->read(true, 'r');
        $json = json_decode($json);
        return $json->lock;
    }
}
