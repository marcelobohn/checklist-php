<?php
	include_once ("../conexaoBD.php");
	$bd = new conexaoBD();	
	$sql = "select * from registro r where 1=1 ";
	$params = array();
	if (($_REQUEST['cliente'] ?? "") != "") {
		$sql .= "  and base = ?";
		$params[] = $_REQUEST['cliente'];
	}
	if (($_REQUEST['tarefa'] ?? "") != "") {
		$sql .= "  and tarefa = ?";
		$params[] = $_REQUEST['tarefa'];
	}
	$sql .= " order by data desc";
	$rows = $bd->query( $sql, $params )->fetchAll();
	echo "Registros filtrados:  <br /><select id=\"idRegistro\">";
	foreach( $rows as $r ){
		echo "<option value=".$r['idRegistro']." >".$r['data']."</option>";
	}
	echo "</select>";
	echo "<a href=\"javascript:mostra()\"> Mostrar check list</a><br />";	
?>