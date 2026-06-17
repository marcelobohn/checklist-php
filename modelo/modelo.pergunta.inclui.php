<?php require_once(__DIR__ . "/../block.php"); ?>
<?php require_once(__DIR__ . "/../csrf.php"); ?>
<?php
	$bd = new \App\ConexaoBD();
	
	$sql = "select max(ordem) reg from modelopergunta where idModelo = ?";
	$r = $bd->query( $sql, array( $_REQUEST["idModelo"] ) )->fetch();
	$ordem = $r['reg'];

	$sql = "insert into modelopergunta (idModelo, idPergunta, ordem) values ( ?, ?, ? )";
	try {
		$bd->query( $sql, array( $_REQUEST["idModelo"], $_REQUEST["idPergunta"], ++$ordem ) );
		echo "Incluido com sucesso!";
	} catch (PDOException $e) {
		echo "Erro na inclusão! ";
	}
?>