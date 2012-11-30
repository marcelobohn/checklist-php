<html xml:lang="pt-br" xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
	<head>
		<meta http-equiv="content-language" content="pt" />
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
</head>
<body>
<?php
	//header("Content-Type: text/html; charset=ISO-8859-1",true);
	//echo $_REQUEST['registro']."<br />";
	include_once ("../conexaoBD.php");
	$bd = new conexaoBD();	
	echo "<form name=\"checklist\" method=\"POST\">\n";

	$sql = "select * from registro r where r.idregistro = ".$_REQUEST['registro'];
	$result = mysql_query( $sql );
	//$r = mysql_fetch_row( $result );
	$r = mysql_fetch_assoc( $result );
	
	echo "<div class=\"pergunta\" >\n";	
	echo "Usuario: ".$r['usuario'];
	echo "</div>\n";		
	echo "<div class=\"pergunta\" >\n";	
	echo "Versão: ".$r['versao'];
	echo "</div>\n";			
	echo "<div class=\"pergunta\" >\n";	
	echo "Cliente: ".$r['base'];
	echo "</div>\n";		
	echo "<div class=\"pergunta\" >\n";	
	echo "Tarefa: ".$r['tarefa'];
	echo "</div>\n";
	echo "<div class=\"pergunta\" >\n";	
	echo "Código Cliente: ".$r['codCliente'];
	echo "</div>\n";
	
	$sql = "select p.*, r.descricao resp, r.idresposta from registroitem ri join pergunta p on ri.idpergunta = p.idpergunta left join resposta r on ri.idresposta = r.idresposta where ri.idregistro = ".$_REQUEST['registro'];
	$result = mysql_query( $sql );
	$registros = mysql_num_rows( $result );

	$numero = 0;
	if( $registros > 0 )	{
		while( $r = mysql_fetch_array( $result ) ){
			$numero++;
			echo "<div class=\"pergunta\" id=\"p_".$r['idPergunta']."\">\n";
			echo "$numero) <i>".utf8_decode($r['descricao'])."</i>";
			if ($r['marcar']=='S') {
				echo "<br />";
				echo ($r['idresposta']=='1') ? ('SIM') : ('NÃO');
			} else {
				if ($r['resposta']=='S') {
					echo "<br>".$r['resp'];
				}
			}
			echo "</div>\n";
		}
	}
	echo "</form>";
	echo "<input type=\"button\" value=\"Limpa\" onclick=\"limpa()\">";
?>	
</body>
</html>
