<?php require_once(__DIR__ . "/../block.php"); ?>
<?php
	$control = new \App\PerguntaControl();
	echo $control->getListaResposta($_REQUEST['id']);	
	if (isset($control)) { unset($control); } 
?>