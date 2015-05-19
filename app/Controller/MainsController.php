<?php
App::uses('File', 'Utility');

class MainsController extends AppController {
	public $layout = "indexs";

	public function index(){
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
	}

	
}
?>