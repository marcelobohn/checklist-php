<?php
	include_once ("../conexaoBD.php");

	$bd = new conexaoBD();
	$sql = "insert into resposta ".
	"(idPergunta, descricao) ".
	"values ".
	//"( 1, '".$_REQUEST["descricao"]."' )";
	"( ".$_REQUEST["idPergunta"].", '".$_REQUEST["descricao"]."' )";	
	//echo $sql."<br />";
	$result = mysql_query( $sql );		
	if( !$result ) {
	    echo "Erro na inclusão!";
	} else {
	    echo "Incluido com sucesso!";	
	}	
?>