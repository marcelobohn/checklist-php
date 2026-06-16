<?php
	include_once ("../conexaoBD.php");
	$bd = new conexaoBD();
	$sql = "delete from modelopergunta where idModelo = ? and idPergunta = ?";
	try {
		$bd->query( $sql, array( $_REQUEST["idModelo"], $_REQUEST["idPergunta"] ) );
		echo "Excluido com sucesso!";
	} catch (PDOException $e) {
		echo "Erro na exclusão!";
	}
?>