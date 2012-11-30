<?php
	include_once ("../conexaoBD.php");
	$bd = new conexaoBD();	
	$sql = "select * from registro r where 1=1 ";
	//if (!isset($_REQUEST['cliente'])) {
	if ($_REQUEST['cliente']!="") {	
		$sql .="  and base = '".$_REQUEST['cliente']."'";
	}
	if ($_REQUEST['tarefa']!="") {	
		$sql .="  and tarefa = ".$_REQUEST['tarefa']."";
	}	
	$sql .=" order by data desc";
	//echo $sql;
	$result = mysql_query( $sql );
	$registros = mysql_num_rows( $result );
	echo "Registros filtrados:  <br /><select id=\"idRegistro\">";
	if( $registros > 0 )	{
		while( $r = mysql_fetch_array( $result ) ){
			echo "<option value=".$r['idRegistro']." >".$r['data']."</option>";			
		}
	}
	echo "</select>";
	echo "<a href=\"javascript:mostra()\"> Mostrar check list</a><br />";	
?>