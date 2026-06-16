<?php
	include_once ("../conexaoBD.php");

	$bd = new conexaoBD();
	$sql = "insert into resposta (idPergunta, descricao) values ( ?, ? )";
	try {
		$bd->query( $sql, array( $_REQUEST["idPergunta"], $_REQUEST["descricao"] ) );
		echo "Incluido com sucesso!";
	} catch (PDOException $e) {
		echo "Erro na inclusão!";
	}
?>