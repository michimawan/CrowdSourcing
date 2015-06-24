<?php
$line = array_merge($datas[0]['Status'], $datas[0]['KomentarStatus']);

$this->CSV->addRow(array_keys($line));
foreach ($datas as $data){
	$line = array_merge($data['Status'],$data['KomentarStatus']);
	$this->CSV->addRow($line);
}
$filename = 'status';
echo $this->CSV->render($filename);
