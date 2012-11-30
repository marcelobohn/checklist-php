<?php
	include_once ("../conexaoBD.php");
	$bd = new conexaoBD();
	$sql = "delete from modelopergunta ".
	"where idModelo = ".$_REQUEST["idModelo"]." and idPergunta = ".$_REQUEST["idPergunta"]." ";	
	$result = mysql_query( $sql );		
	if( !$result ) {
	    echo "Erro na exclusão!";
	} else {
	    echo "Excluido com sucesso!";	
	}	
?>