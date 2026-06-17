<?php require_once(__DIR__ . "/../block.php"); ?>
<?php require_once(__DIR__ . "/../csrf.php"); ?>
<?php
	$bd = new \App\ConexaoBD();
	
	$bd->limpaTabela('registro');
	$bd->limpaTabela('registroitem');

	$bd->limpaTabela('modelo');
	$bd->limpaTabela('modelopergunta');
	
	$bd->limpaTabela('pergunta');		
	$bd->limpaTabela('resposta');		

?>