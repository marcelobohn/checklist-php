<?php
	header("Content-Type: text/html; charset=ISO-8859-1",true);
	include_once ("../conexaoBD.php");
	$bd = new conexaoBD();	
	$sql = "select * from modelo m ";
	$result = mysql_query( $sql );
	$registros = mysql_num_rows( $result );
	echo "Modelos dipon&iacute;veis: <br /><select id=\"idModelo\" style=\"width:400px;\">";
	if( $registros > 0 )	{
		while( $r = mysql_fetch_array( $result ) ){
			echo "<option value=".$r['idModelo']." >".utf8_decode($r['nome'])."</option>";			
		}
	}
	echo "</select>";
	echo  "<a href=\"javascript:gera()\"> Montar check list</a><br />";
	echo  "<div id=\"registro\"></div>";
	
?>	