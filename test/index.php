<?php
date_default_timezone_set('Europe/Moscow');
function __autoload($className){
	$file = str_replace('\\','/',$className);
	require_once '../src/'.$file.'.php';
	return true;
}
$cf = new \vsb\ConturFocus;
//echo "Autocomplete\n";
//print_r($cf->Autocomplete('Моби'));
//echo "Search\n";
//print_r($cf->Search('Мобиплас'));
echo "Entity\n";
print_r($cf->Entity('7703666220','1087746636328'));
?>
