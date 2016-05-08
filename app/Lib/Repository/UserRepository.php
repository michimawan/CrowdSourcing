<?php
App::uses('Model', 'Model');
App::import('Repository', 'KomentarRepository');
App::import('Lib', 'SettingManager');

class UserRepository
{
    private $uses = ['Status', 'Komentar', 'Label', 'User'];
    public function __construct()
    {
        foreach($this->uses as $use)
            App::import('Model', $use);

        $this->userModel = new $this->uses[3]();
    }

    public function count()
    {
        return $this->userModel->find('count');
    }

    public function incrementLabelForAUser($social_network_id)
    {
        $this->userModel->updateAll(
            ['User.total_label' => 'User.total_label+1'],
            ['User.social_network_id' => $social_network_id]
        );
    }

    public function getRandomKomentarForAUser($social_network_id)
    {
        $komentarRepository = new KomentarRepository();

        //baca maksimum label per komentar
        $maxLabel = (new SettingManager('setting.txt'))->getN();

        //ambil username user
        $this->userModel->unbindModel(['hasMany' => ['Label']]);
        $users = $this->userModel->find('first', [
            'conditions' => ['User.social_network_id' => $social_network_id],
            'fields' => ['User.email'],
        ]);
        $userEmail = $users['User']['email'];

        return $komentarRepository->getModel()->getRandom($userEmail, $maxlabel);
    }
}
