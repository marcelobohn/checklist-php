<?php require_once(__DIR__ . "/../block.php"); ?>
<html xml:lang="pt-br" xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
	<head>
		<meta http-equiv="content-language" content="pt" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<?php
	//header("Content-Type: text/html; charset=UTF-8",true);
	//echo $_REQUEST['registro']."<br />";
	$bd = new \App\ConexaoBD();	
	echo "<form name=\"checklist\" method=\"POST\">\n";

	$sql = "select * from registro r where r.idregistro = ?";
	$r = $bd->query( $sql, array( $_REQUEST['registro'] ) )->fetch();

	echo "<div class=\"pergunta\" >\n";	
	echo "Usuario: ".h($r['usuario']);
	echo "</div>\n";
	echo "<div class=\"pergunta\" >\n";
	echo "Versão: ".h($r['versao']);
	echo "</div>\n";
	echo "<div class=\"pergunta\" >\n";
	echo "Cliente: ".h($r['base']);
	echo "</div>\n";
	echo "<div class=\"pergunta\" >\n";
	echo "Tarefa: ".h($r['tarefa']);
	echo "</div>\n";
	echo "<div class=\"pergunta\" >\n";
	echo "Código Cliente: ".h($r['codCliente']);
	echo "</div>\n";
	
	$sql = "select p.*, r.descricao resp, r.idresposta from registroitem ri join pergunta p on ri.idpergunta = p.idpergunta left join resposta r on ri.idresposta = r.idresposta where ri.idregistro = ?";
	$rows = $bd->query( $sql, array( $_REQUEST['registro'] ) )->fetchAll();

	$numero = 0;
	foreach( $rows as $r ){
			$numero++;
			echo "<div class=\"pergunta\" id=\"p_".$r['idPergunta']."\">\n";
			echo "$numero) <i>".h($r['descricao'])."</i>";
			if ($r['marcar']=='S') {
				echo "<br />";
				echo ($r['idresposta']=='1') ? ('SIM') : ('NÃO');
			} else {
				if ($r['resposta']=='S') {
					echo "<br>".h($r['resp']);
				}
			}
			echo "</div>\n";
	}
	echo "</form>";
	echo "<input type=\"button\" value=\"Limpa\" onclick=\"limpa()\">";
?>	
</body>
</html>
