<?php
	header("Content-Type: text/html; charset=UTF-8",true);
	require_once("../block.php");
	include_once ("modelo.control.php");
	$control = new ModeloControl();
	echo $control->getListaPergunta($_REQUEST['id']);	
	if (isset($control)) { unset($control); } 
?>