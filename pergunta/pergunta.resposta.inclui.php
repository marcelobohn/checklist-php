<?php require_once(__DIR__ . "/../block.php"); ?>
<?php require_once(__DIR__ . "/../csrf.php"); ?>
<?php

	$bd = new \App\ConexaoBD();
	$sql = "insert into resposta (idPergunta, descricao) values ( ?, ? )";
	try {
		$bd->query( $sql, array( $_REQUEST["idPergunta"], $_REQUEST["descricao"] ) );
		echo "Incluido com sucesso!";
	} catch (PDOException $e) {
		echo "Erro na inclusão!";
	}
?>