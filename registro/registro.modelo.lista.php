<?php require_once(__DIR__ . "/../block.php"); ?>
<?php
	header("Content-Type: text/html; charset=UTF-8",true);
	include_once ("../conexaoBD.php");
	$bd = new conexaoBD();	
	$sql = "select * from modelo m ";
	$rows = $bd->query( $sql )->fetchAll();
	echo "Modelos dipon&iacute;veis: <br /><select id=\"idModelo\" style=\"width:400px;\">";
	foreach( $rows as $r ){
		echo "<option value=".$r['idModelo']." >".$r['nome']."</option>";
	}
	echo "</select>";
	echo  "<a href=\"javascript:gera()\"> Montar check list</a><br />";
	echo  "<div id=\"registro\"></div>";
	
?>	