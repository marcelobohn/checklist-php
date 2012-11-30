<?php
	include_once ("../conexaoBD.php");
	$bd = new conexaoBD();
	
	$bd->limpaTabela('registro');
	$bd->limpaTabela('registroitem');

	$bd->limpaTabela('modelo');
	$bd->limpaTabela('modelopergunta');
	
	$bd->limpaTabela('pergunta');		
	$bd->limpaTabela('resposta');		

?>