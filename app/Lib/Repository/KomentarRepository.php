<?php
App::uses('Model', 'Model');

class KomentarRepository
{
    private $uses = ['Status', 'Komentar', 'Label'];
    public function __construct()
    {
        foreach($this->uses as $use)
            App::import('Model', $use);

        $this->komentarModel = new $this->uses[1]();
    }

    public function count()
    {
        return $this->komentarModel->find('count');
    }

    public function getAllCommentRelatedToStatus($id = null)
    {
        if($id) {
            $this->komentarModel->unbindModel(['hasMany' => ['Label']]);
            return $this->komentarModel->find('all', [
                'conditions' => ['Komentar.id_status' => $id],
            ]);
        }
        return [];
    }

    public function getAllComment()
    {
        $this->komentarModel->unbindModel(['hasMany' => ['Label']]);
        return $this->komentarModel->find('all', [
            'order' => ['Komentar.id_status'],
        ]);
    }

    public function getMaxLabelInAComment()
    {
        return $this->komentarModel->find('all', [
            'fields' => ['MAX(Komentar.jml_label) as maxLabelCount'],
            ]);
    }

    public function setStatusToWhereJmlLabel($status, $labelCount)
    {
        $this->komentarModel->unbindModel(['hasMany' => ['Label']]);
        $this->komentarModel->unbindModel(['belongsTo' => ['Status']]);
        $this->komentarModel->updateAll([
            'Komentar.status' => '"' . $status . '"'], [
                'Komentar.jml_label' => $labelCount
            ]);
    }

    public function getUnfinishedLabeledComment()
    {
        $this->komentarModel->unbindModel(['hasMany' => ['Label']]);
        $this->komentarModel->unbindModel(['belongsTo' => ['Status']]);
        return $this->komentarModel->find('all', [
            'conditions' => ['Komentar.status' => 'belum'],
        ]);
    }

    public function determineFinalLabelForEveryComment()
    {
        $comments = $this->komentarModel->find('all');

        $komenDataSource = $this->komentarModel->getDataSource();
        try {
            $komenDataSource->begin();
            foreach($comments as $comment) {
                $value = 0;
                foreach ($comment['Label'] as $label) {
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

                $label = ['id_komentar' => $comment['Komentar']['id_komentar'], 'label' => $value];
                $this->komentarModel->save($label);
            }
            $komenDataSource->commit();
            return true;
        } catch(Exception $e) {
            $komenDataSource->rollback();
            return false;
        }
    }

    public function getModel()
    {
        return $this->komentarModel;
    }
}
