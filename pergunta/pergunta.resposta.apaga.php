<?php require_once(__DIR__ . "/../block.php"); ?>
<?php
	include_once ("../conexaoBD.php");
	$bd = new conexaoBD();
	$sql = "delete from resposta where idResposta = ? and idPergunta = ?";
	try {
		$bd->query( $sql, array( $_REQUEST["idResposta"], $_REQUEST["idPergunta"] ) );
		echo "Excluido com sucesso!";
	} catch (PDOException $e) {
		echo "Erro na exclusão!";
	}
?>