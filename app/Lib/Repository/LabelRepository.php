<?php
App::uses('Model', 'Model');
App::import('Repository', 'KomentarRepository');

class LabelRepository
{
    private $uses = ['Status', 'Komentar', 'Label'];
    public function __construct()
    {
        foreach($this->uses as $use)
            App::import('Model', $use);

        $this->labelModel = new $this->uses[2]();
    }

    public function count()
    {
        return $this->labelModel->find('count');
    }

    public function getAllLabelRelatedToUser($userEmail = null)
    {
        if($userEmail)
            return $this->labelModel->find('all', [
                'conditions' => ['Label.username_pelabel' => $userEmail],
                'recursive' => -1,
                'fields' => ['Label.nama_label']
            ]);
        return [];
    }

    public function getAllLabel()
    {
        $this->labelModel->unbindModel(['belongsTo' => ['Komentar', 'User']]);
        return $this->labelModel->find('all');
    }

    public function getModel()
    {
        return $this->labelModel;
    }

    public function checkLabellingPermissionForAUser($social_network_id)
    {
        $komentarRepository = new KomentarRepository();
        $commentCount = $komentarRepository->count();
        //lihat user sudah melabeli berapa komentar
        $labelsekarang = $komentarRepository->getModel()->countLabelForAUser($social_network_id);

        //buat permissionnya
        $cannotLabellingAgain = false;
        if($labelsekarang[0][0]['jumlah'] == $commentCount)
            $cannotLabellingAgain = true;

        return $cannotLabellingAgain;
    }
}
