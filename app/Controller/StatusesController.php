<?php
App::import('Lib', 'SettingManager');
App::import('Repository', 'KomentarRepository');
App::import('Repository', 'StatusRepository');
App::import('Repository', 'LabelRepository');
App::import('Repository', 'UserRepository');

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
        $this->set('title','Daftar Status Facebook');

        $currentUser = $this->Auth->user();
        if($currentUser['role'] == 'user')
            $this->redirect(['controller' => 'users', 'action' => 'user']);

        $this->Paginator->settings = [
            'limit' => 5,
            'recursive' => 0,
            'paramType' => 'querystring',
            'limit' => 5,
            'maxLimit' => 100,
        ];
        $datas = $this->Paginator->paginate('Status');

        //lock hanya muncul jika semua data sudah terlabeli
        $lengkap = (new KomentarRepository())->getUnfinishedLabeledComment();
        if($lengkap);
        else {
            $lockstate = $this->settingGetLock();
            $this->set(['lockstate' => $lockstate]);
        }

        $this->set(['datas' => $datas]);
    }

    // priviledge: admin only
    // method for showing all a single status and all comment related to it
    public function view($id = null)
    {
        $currentUser = $this->Auth->user();
        if($currentUser['role']=='user')
            $this->redirect(['controller' => 'users', 'action' => 'user']);

        if($id){
            $this->set('title','Status Facebook');

            $status = (new StatusRepository())->getStatusByIdStatus($id);

            $this->Paginator->settings = [
                'conditions' => ['Komentar.id_status' => $id],
                'recursive' => 1,
                'paramType' => 'querystring',
                'limit' => 5,
                'maxLimit' => 100,
            ];
            $komentars = $this->Paginator->paginate('Komentar');
            $maxlabel = $this->settingGetN();

            $this->set([
                'status' => $status,
                'komentars' => $komentars,
                'maxlabel' => $maxlabel,
            ]);
        } else {
            $this->redirect(['action' => 'index']);
        }
    }

    // priviledge: user
    // method for user can labeling a comment or not
    public function labeling($user = null, $id_komen = null, $id_status = null, $label = null)
    {
        $currentUser = $this->Auth->user();
        if($currentUser['role']=='admin')
            $this->redirect(['controller' => 'users', 'action' => 'index']);

        $cannotLabellingAgain = (new LabelRepository())->checkLabellingPermissionForAUser($currentUser['social_network_id']);

        if($cannotLabellingAgain == true)
            $this->redirect(['controller' => 'users', 'action' => 'user']);

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

                (new UserRepository())->incrementLabelForAUser($currentUser['social_network_id']);

                $this->inckomenlabel($id_komen);
                $this->Session->setFlash('Komentar "$id_komen" berhasil dilabeli', 'customflash', ['class' => 'success']);
            }
            $this->redirect(['action' => 'labeling', $currentUser['social_network_id']]);
        } else if ($currentUser['social_network_id']) {
            $this->set('title', 'Pelabelan Komentar');

            $datas = (new UserRepository())->getRandomKomentarForAUser($currentUser['social_network_id']);
            if($datas)
                $this->set(['datas' => $datas]);
            else
                $this->redirect(['controller' => 'users', 'action' => 'user', $currentUser['social_network_id']]);
        }
    }

    // priviledge: user
    // method for user edit label that has given to a comment
    public function edit($id_komentar = null, $id_label = null)
    {
        if($this->Auth->user()['role']=='admin')
            $this->redirect(['controller' => 'users', 'action' => 'index']);

        $this->set('title', 'Edit Label Komentar');
        if($this->settingGetLock() == 'true')
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
        $this->autoLayout = false;

        $datas = (new KomentarRepository())->getAllComment();
        $this->set(['datas' => $datas]);
    }

    // method for export single status, and all comment that related to it
    public function expStatus($id)
    {
        $this->layout = null;
        $this->autoLayout = false;

        $datas = (new KomentarRepository())->getAllCommentRelatedToStatus($id);
        $this->set(['datas' => $datas]);
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
        if($jmllabel['Komentar']['jml_label'] == $this->settingGetN()) {
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

    private function settingGetN()
    {
        return (new SettingManager('setting.txt'))->getN();
    }

    private function settingGetLock()
    {
        return (new SettingManager('setting.txt'))->getLock();
    }
}
