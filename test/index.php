<?php
date_default_timezone_set('Europe/Moscow');
function __autoload($className){
	$file = str_replace('\\','/',$className);
	require_once '../src/'.$file.'.php';
	return true;
}
$cf = new \vsb\ConturFocus;
print_r($cf->Search('Мобиплас'));
?>
