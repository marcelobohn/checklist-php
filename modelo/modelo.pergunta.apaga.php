<?php require_once(__DIR__ . "/../block.php"); ?>
<?php require_once(__DIR__ . "/../csrf.php"); ?>
<?php
	$bd = new \App\ConexaoBD();
	$sql = "delete from modelopergunta where idModelo = ? and idPergunta = ?";
	try {
		$bd->query( $sql, array( $_REQUEST["idModelo"], $_REQUEST["idPergunta"] ) );
		echo "Excluido com sucesso!";
	} catch (PDOException $e) {
		echo "Erro na exclusão!";
	}
?>