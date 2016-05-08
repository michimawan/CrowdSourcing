<?php
App::uses('Model', 'Model');

class StatusRepository
{
    private $uses = ['Status', 'Komentar', 'Label'];
    public function __construct()
    {
        foreach($this->uses as $use)
            App::import('Model', $use);

        $this->statusModel = new $this->uses[0]();
    }

    public function count()
    {
        return $this->statusModel->find('count');
    }

    public function getStatusByIdStatus($id_status = null)
    {
        $this->statusModel->unbindModel(['hasMany' => ['Komentar']]);
        return $this->statusModel->find('all', [
            'conditions' => ['Status.id_status' => $id_status],
        ]);
    }
}
