<?php
$line = array_merge($datas[0]['Status'], $datas[0]['Komentar']);

$this->CSV->addRow(array_keys($line));
foreach ($datas as $data){
	$line = array_merge($data['Status'],$data['Komentar']);
	$this->CSV->addRow($line);
}
$filename = 'status';
echo $this->CSV->render($filename);
