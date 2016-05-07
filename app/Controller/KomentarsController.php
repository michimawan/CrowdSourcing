<?php
class KomentarsController extends AppController
{
    public $uses = ['Komentar'];
    public $layout = 'layout';

    public function index($id = null)
    {
        if($id) {
            $this->set('title','Daftar Komentar Facebook');
            $datas = $this->Komentar->find('all', [
                'conditions' => ['Komentar.id_komentar' => $id]
            ]);
            $this->set(['datas' => $datas]);
        } else {
            $this->redirect(['controller' => 'statuses', 'action' => 'index']);
        }
    }
}
