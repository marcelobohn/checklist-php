<?php
/*
	if ($_POST) {
		foreach ($_POST as $key => $value) {
			echo '<br />' . $key . ' = ' . $value;
		}
	}
	
	echo $_REQUEST['usuario'];
*/	

	$rand = rand(100000,999999);
	$resultado = false;
	
	include_once ("../conexaoBD.php");
	$bd = new conexaoBD();

	$sql = "insert into registro ".
	"(idModelo, rand, data, usuario, versao, base, tarefa, codCliente) ".
	"values ".
	"(".$_REQUEST["idModelo"].", ".$rand.", CURRENT_TIMESTAMP, '".$_POST["usuario"]."', '".$_POST["versao"]."', '".$_POST["base"]."', '".$_POST["tarefa"]."', '".$_POST["cliente"]."' )";	
	$result = mysql_query( $sql );		
	if( !$result ) {
	    echo "Erro na inclusão do registro! [1]".$bd->chkOnLine($sql)."<br/>";
	} else {
	    echo "Incluido com sucesso!<br/>";	
		
		$sql = "select idRegistro from registro where rand = ".$rand."";
		$result = mysql_query( $sql );
		$r = mysql_fetch_row( $result );
		$registro = $r[0];
		echo "Registro gerado: ".$registro."<br/>";		
		$resultado = true;
	}	


	if (($_POST) && ($resultado)) {
		foreach ($_POST as $key => $value) {
			if (substr($key,0,2) == "r_") {
			//echo '<br />' . $key . ' = ' . $value;
			$pergunta = substr($key,2);
			//$pergunta = parseInt($pergunta);
			$sql = "insert into registroitem (idRegistro, idPergunta, idResposta) values ";
			$sql .= "(".$registro.", ".$pergunta.", ".$value.")";	
			//echo $sql;
			$result = mysql_query( $sql );		
			if( !$result ) {
				echo "Erro na inclusão ! [2]".$sql."<br/>";
				$resultado = false;
			} else {
				echo "Incluido com sucesso!<br/>";				
			}
			}
			
		}
	}
	//header("Location: index.php");
	if ($resultado) {
		echo "<script type=\"text/javascript\">";
		echo "function espera(){ ";
		echo "window.location = \"index.php\" } ";
		echo "setTimeout('espera()', 2000)";
		echo "</script>";
		echo "<h1>Gravado com sucesso</h1>";
	} else {
		echo "<h1>Erros durante a gravação</h1>";
		echo "<a href=\"index.php\">voltar</a>";	
	}

?>