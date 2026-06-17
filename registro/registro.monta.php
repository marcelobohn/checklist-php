<?php require_once(__DIR__ . "/../block.php"); ?>
<html xml:lang="pt-br" xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
	<head>
		<meta http-equiv="content-language" content="pt" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<?php
	//header("Content-Type: text/html; charset=UTF-8",true);
	$bd = new \App\ConexaoBD();	
	$sql = "select * from pergunta p join modelopergunta mp on p.idpergunta = mp.idpergunta where mp.idmodelo = ? order by mp.ordem";
	$rows = $bd->query( $sql, array( $_REQUEST['modelo'] ) )->fetchAll();
	echo "<form name=\"checklist\" method=\"POST\">\n";
	//echo "<input type=\"text\" id=\"idModelo\" value=\"".$_REQUEST['modelo']."\" /><br />\n"; //hidden
	echo "<label style=\"width:180px;\">Usuario: </label><input type=\"text\" name=\"usuario\" id=\"usuario\" style=\"width:400px;\"><br />";
	echo "<label style=\"width:180px;\">Versão: </label><input type=\"text\" name=\"versao\" id=\"versao\" style=\"width:200px;\"><br />";
	echo "<label style=\"width:180px;\">Cliente: </label><input type=\"text\" name=\"base\" id=\"base\" style=\"width:200px;\"><br />";
	echo "<label style=\"width:180px;\">Tarefa: </label><input type=\"text\" name=\"tarefa\" id=\"tarefa\" style=\"width:200px;\"><br />";
	echo "<label style=\"width:180px;\">Código Cliente: </label><input type=\"text\" name=\"cliente\" id=\"cliente\" style=\"width:200px;\"><br />";
	
	$numero = 0;
	foreach( $rows as $r ){
		$numero++;
		echo "<div class=\"pergunta\" id=\"p_".$r['idPergunta']."\">\n";
		echo "$numero) <i>".h($r['descricao'])."</i> [".$r['idPergunta']."]";
		if ($r['marcar']=='S') {
			echo "<br /><input type=\"radio\" name=\"r_".$r['idPergunta']."\" value=\"1\" /> sim <br />\n";
			echo "<input type=\"radio\"  name=\"r_".$r['idPergunta']."\" value=\"0\" /> não <br />";
		} else {
			if ($r['resposta']=='S') {
				echo "<br />\n";
				$respostas = $bd->query( "select * from resposta where idPergunta = ?", array( $r['idPergunta'] ) )->fetchAll();
				foreach( $respostas as $rp ){
					echo " <input type=\"radio\" name=\"r_".$rp['idPergunta']."\" value=\"".$rp["idResposta"]."\"> ".h($rp['descricao'])."<br />\n";
				}
			}
		}
		echo "</div>\n";
	}
	echo "</form>";
	echo "<input type=\"button\" value=\"Gravar\" onclick=\"valida()\"><input type=\"button\" value=\"Cancela\" onclick=\"cancela()\">";
?>	
</body>
</html>
