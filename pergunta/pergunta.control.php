<?php
include_once ("../conexaoBD.php");

class PerguntaControl {

	/* Conexão com o banco de dados */
	var $bd;
	
	//construtor
	function PerguntaControl(){
		$this->bd = new conexaoBD();		
	}
	
	function getListaResposta($id) {
		//header("Content-Type: text/html; charset=ISO-8859-1",true);
		$resposta = "";
		$resposta .=  "<span style=\"font-size:16px; float:left;\">Respostas</span> &nbsp;&nbsp;&nbsp; ";
		$resposta .=  "<a href=\"javascript:incluiResposta(idPergunta.value);;listaResposta(true)\">Inclui resposta</a><br />";
		$resposta .= "<ul>";
		$sql = "select * from resposta ";
		$sql .= "  where idPergunta = ".$id."";	
		$result = mysql_query( $sql );
		$registros = mysql_num_rows( $result );
		
		if( $registros > 0 )	{
			while( $r = mysql_fetch_array( $result ) ){
				$resposta .= "<li>".$r['descricao']."&nbsp;&nbsp;<a href=\"javascript:apagaResposta(".$id.",".$r['idResposta'].");listaResposta(true)\"><b>X</b></a> </li>";			
			}
		}
		echo "</ul>";	
		return $resposta;
	}

	function getLista($p,$pag) {
		//header("Content-Type: text/html; charset=ISO-8859-1",true);
		//header('Content-Type: text/html; charset=utf-8'); 
		$resposta = "";
		$sql = "select count(*) from pergunta where 1=1 ";
		if ($p != null) { $sql .= "  and descricao like '".$p."%'"; }		
		list($totalReg) = mysql_fetch_array(mysql_query($sql));
		$itensPagina = 10;
		$ini = $pag - 1;
		$ini = $ini * $itensPagina;
		$fim = $ini + $itensPagina;
		
		$sql = "select * from pergunta where 1=1 ";
		if ($p != null) { $sql .= "  and descricao like '".$p."%'"; }
		$sql .= "  order by descricao ";
		$sql .= "  LIMIT $ini, $itensPagina ";

		$totalPaginas = ceil($totalReg/$itensPagina);
		for ($i = 1; $i <= $totalPaginas; $i++) {
			if ($pag != $i) { $resposta .= "<a href=\"javascript:lista(".$i.")\">".$i."</a>"; } 
			else { $resposta .= $i; }
			if ($i != $totalPaginas) { $resposta .= " - "; }
		}
		$resposta .= " | Registros: ".$totalReg;		
		
		$result = mysql_query( $sql );
		if( mysql_num_rows( $result ) > 0 )	{
			$resposta .= "<table border='0'><tr>".
			"<th></th>".
			"<th>C&oacute;digo</th>".
			"<th>Descricao</th>".
			"<th>Marca</th>".
			"<th>Resposta</th>".
			"</tr>";
			// style=\" border-bottom: 1px solid black;\"
			while( $r = mysql_fetch_array( $result ) ){
				$resposta = $resposta . 
				"\n<tr> " . 
				"<td><input type=\"checkbox\" value=\"".$r[0]."\"></td>" . 
				"<td>".$r[0]."</td>"; 
				if ($_SESSION['perfil']=='adm') { $resposta .= 
					"<td><a href=\"javascript:altera(".$r[0].");\">".utf8_decode($r['descricao'])."</a></td>"; } 
				else { $resposta .= 
					"<td>".utf8_decode($r['descricao'])."</td>"; }
				$resposta .=
				"<td>".$r['marcar']."</td>" . 
				"<td>".$r['resposta']."</td>" . 
				"</tr>";
			}
			$resposta = $resposta . "</table>\n";
			
		}
		else
			$resposta = "Não foi encontrado nenhum dado.";
		return $resposta;
	}
	
	function inserir($model){
		//header("Content-Type: text/html; charset=ISO-8859-1",true);
		//header('Content-Type: text/html; charset=utf-8');
		$sql = "insert into pergunta ".
		"(descricao, marcar, resposta) ".
		"values ".
		"( '".utf8_encode($model->descricao)."', '".$model->marcar."', '".$model->resposta."' )";
		$result = mysql_query( $sql );		
		if( !$result ) {
		    return false;
		}	
		return true;		
	}
	
	function atualizar($model){		
		//header("Content-Type: text/html; charset=ISO-8859-1",true);
		//header('Content-Type: text/html; charset=utf-8');
		$sql = "update pergunta set ".
		"descricao = '".utf8_encode($model->descricao)."' , ".
		"marcar = '".$model->marcar."' , ".
		"resposta = '".$model->resposta."' ".
		"where idPergunta = ".$model->idPergunta;
		//echo $sql."<br />";
		$result = mysql_query( $sql );		
		if( !$result ) {
		    return false;
		}	
		return true;		
	}

	function apagar($id){
		$sql = "delete from pergunta where idPergunta = ".$id." ";
		$result = mysql_query( $sql );		
		if( !$result ) {
		    return false;
		}	
		return true;		
	}
	
}