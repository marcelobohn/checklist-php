<?php
	include_once ("../conexaoBD.php");
	$bd = new conexaoBD();
	
	$sql = "select max(ordem) reg from modelopergunta where idModelo = ".$_REQUEST["idModelo"];
	$result = mysql_query( $sql );
	$r = mysql_fetch_assoc( $result );
	$ordem = $r['reg'];
	
	$sql = "insert into modelopergunta ".
	"(idModelo, idPergunta, ordem) ".
	"values ".
	"( ".$_REQUEST["idModelo"].", ".$_REQUEST["idPergunta"].", ".++$ordem." )";	
	$result = mysql_query( $sql );		
	if( !$result ) {
	    echo "Erro na inclusão! ";
	} else {
	    echo "Incluido com sucesso!";	
	}	
?>