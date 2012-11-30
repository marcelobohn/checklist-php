<?php
	include_once ("pergunta.control.php");
	$control = new PerguntaControl();
	echo $control->getListaResposta($_REQUEST['id']);	
	if (isset($control)) { unset($control); } 
?>