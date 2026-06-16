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
	"values (?, ?, CURRENT_TIMESTAMP, ?, ?, ?, ?, ?)";
	try {
		$bd->query( $sql, array(
			$_REQUEST["idModelo"], $rand,
			$_POST["usuario"], $_POST["versao"], $_POST["base"], $_POST["tarefa"], $_POST["cliente"]
		) );
		echo "Incluido com sucesso!<br/>";
		$registro = $bd->con->lastInsertId();
		echo "Registro gerado: ".$registro."<br/>";
		$resultado = true;
	} catch (PDOException $e) {
		echo "Erro na inclusão do registro! [1]<br/>";
	}


	if (($_POST) && ($resultado)) {
		foreach ($_POST as $key => $value) {
			if (substr($key,0,2) == "r_") {
			//echo '<br />' . $key . ' = ' . $value;
			$pergunta = substr($key,2);
			$sql = "insert into registroitem (idRegistro, idPergunta, idResposta) values (?, ?, ?)";
			try {
				$bd->query( $sql, array( $registro, $pergunta, $value ) );
				echo "Incluido com sucesso!<br/>";
			} catch (PDOException $e) {
				echo "Erro na inclusão ! [2]<br/>";
				$resultado = false;
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