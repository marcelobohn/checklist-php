<?php
	header("Content-Type: text/html; charset=UTF-8",true);
	require_once("../block.php");
	$control = new \App\ModeloControl();
	echo $control->getListaPergunta($_REQUEST['id']);	
	if (isset($control)) { unset($control); } 
?>