<html xml:lang="pt-br" xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
	<head>
		<meta http-equiv="content-language" content="pt" />
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
</head>
<body>
<?php
	//header("Content-Type: text/html; charset=ISO-8859-1",true);
	include_once ("../conexaoBD.php");
	$bd = new conexaoBD();	
	$sql = "select * from pergunta p join modelopergunta mp on p.idpergunta = mp.idpergunta where mp.idmodelo = ".$_REQUEST['modelo'];
	$result = mysql_query( $sql );
	$registros = mysql_num_rows( $result );
	echo "<form name=\"checklist\" method=\"POST\">\n";
	//echo "<input type=\"text\" id=\"idModelo\" value=\"".$_REQUEST['modelo']."\" /><br />\n"; //hidden
	echo "<label style=\"width:180px;\">Usuario: </label><input type=\"text\" name=\"usuario\" id=\"usuario\" style=\"width:400px;\"><br />";
	echo "<label style=\"width:180px;\">Versão: </label><input type=\"text\" name=\"versao\" id=\"versao\" style=\"width:200px;\"><br />";
	echo "<label style=\"width:180px;\">Cliente: </label><input type=\"text\" name=\"base\" id=\"base\" style=\"width:200px;\"><br />";
	echo "<label style=\"width:180px;\">Tarefa: </label><input type=\"text\" name=\"tarefa\" id=\"tarefa\" style=\"width:200px;\"><br />";
	echo "<label style=\"width:180px;\">Código Cliente: </label><input type=\"text\" name=\"cliente\" id=\"cliente\" style=\"width:200px;\"><br />";
	
	$numero = 0;
	if( $registros > 0 )	{
		while( $r = mysql_fetch_array( $result ) ){
			$numero++;
			echo "<div class=\"pergunta\" id=\"p_".$r['idPergunta']."\">\n";
			//echo "<label style=\"width:300px;\"><i>".$r['descricao']."</i> [".$r['idPergunta']."]</label>";
			echo "$numero) <i>".utf8_decode($r['descricao'])."</i> [".$r['idPergunta']."]";
			if ($r['marcar']=='S') {
				//echo "<input type=\"checkbox\" />";
				echo "<br /><input type=\"radio\" name=\"r_".$r['idPergunta']."\" value=\"1\" /> sim <br />\n";
				echo "<input type=\"radio\"  name=\"r_".$r['idPergunta']."\" value=\"0\" /> não <br />";
			} else {
				if ($r['resposta']=='S') {
					//echo "<br /> Repostas: <br />\n";
					echo "<br />\n";
					$respostaResult = mysql_query( "select * from resposta where idPergunta = ".$r['idPergunta'] );
						while( $rp = mysql_fetch_array( $respostaResult ) ){
							echo " <input type=\"radio\" name=\"r_".$rp['idPergunta']."\" value=\"".$rp["idResposta"]."\"> ".utf8_decode($rp['descricao'])."<br />\n";
					}
				}
			}
			echo "</div>\n";						
		}
	}
	echo "</form>";
	echo "<input type=\"button\" value=\"Gravar\" onclick=\"valida()\"><input type=\"button\" value=\"Cancela\" onclick=\"cancela()\">";
?>	
</body>
</html>
