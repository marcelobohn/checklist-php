<?php
include_once ("../conexaoBD.php");

class ModeloControl {

	/* Conexão com o banco de dados */
	var $bd;
	var $tabela = "modelo";
	
	//construtor
	function ModeloControl(){
		$this->bd = new conexaoBD();		
	}
	
	function getListaPergunta($id) {
	//header("Content-Type: text/html; charset=ISO-8859-1",true);
	$resposta = "<br />";
	$sql = "select * from pergunta p where p.idpergunta not in (select idpergunta from modelopergunta mp where mp.idModelo = ".$id.")";
	$result = mysql_query( $sql );
	$registros = mysql_num_rows( $result );
	if( $registros > 0 )	{
		$resposta .= "Perguntas diponíveis: <br /><select id=\"idPergunta\">";
		while( $r = mysql_fetch_array( $result ) ){
			$resposta .= "<option value=".$r['idPergunta']." >".utf8_decode($r['descricao'])."</option>";			
		}
		$resposta .= "</select>";
		$resposta .=  "<a href=\"javascript:incluiPergunta(idModelo.value, idPergunta.value);listaPergunta()\">Inclui pergunta</a><br />";
	}
	$resposta .= "Perguntas selecionadas: <ul style=\"list-style-type: none;\">";
	$sql = "select mp.*, p.descricao from modelopergunta mp";
	$sql .= "  join pergunta p on mp.idPergunta = p.idPergunta";		
	$sql .= "  where idModelo = ".$id." order by ordem";	
	$result = mysql_query( $sql );
	$registros = mysql_num_rows( $result );
	if( $registros > 0 )	{
		$recNo = 0;
		while( $r = mysql_fetch_array( $result ) ){
			$recNo++;
			//echo $r['descricao']." - ".$r['marcar']."<br />";
			$resposta .= "<li>";
			$resposta .= "<a href=\"javascript:apagaPergunta(".$id.",".$r['idPergunta'].");listaPergunta()\"><b>X</b></a>&nbsp;&nbsp;";
/*
			if ($recNo!=1) 
				$resposta .= "<img src=\"../img/arrow_up.png\" onclick=\"alert('clicou')\" style=\"cursor:pointer;\" >";
			else
				$resposta .= "<img width=\"10\">";
			$resposta .= "&nbsp;";
			if ($recNo!=$registros) 
				$resposta .= "<img src=\"../img/arrow_down.png\" onclick=\"alert('clicou')\" style=\"cursor:pointer;\" >";
			else
				$resposta .= "<img width=\"10\">";
			$resposta .= "&nbsp;&nbsp;";
*/
			$resposta .= "".utf8_decode($r['descricao'])."</li>";			
		}
	}
	echo "</ul>";	
	return $resposta;
	}	

	function getLista($p,$pag) {
		//header("Content-Type: text/html; charset=ISO-8859-1",true);
		$resposta = "";
		$sql = "select count(*) from ".$this->tabela." where 1=1 ";
		if ($p != null) { $sql .= "  and nome like '".$p."%'"; }		
		list($totalReg) = mysql_fetch_array(mysql_query($sql));
		$itensPagina = 10;
		$ini = $pag - 1;
		$ini = $ini * $itensPagina;
		$fim = $ini + $itensPagina;
		
		$sql = "select * from ".$this->tabela." where 1=1 ";
		if ($p != null) { $sql .= "  and nome like '".$p."%'"; }
		$sql .= "  order by nome ";
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
			"<th>Código</th>".
			"<th>Nome</th>".
			"</tr>";
			// style=\" border-bottom: 1px solid black;\"
			while( $r = mysql_fetch_array( $result ) ){
				$resposta = $resposta . 
				"\n<tr> " . 
				"<td><input type=\"checkbox\" value=\"".$r[0]."\"></td>" . 
				"<td>".$r[0]."</td>"; 
				if ($_SESSION['perfil']=='adm') { $resposta .= 
					"<td><a href=\"javascript:altera(".$r[0].");\">".utf8_decode($r['nome'])."</a></td>"; } 
				else { $resposta .= 
					"<td>".utf8_decode($r['nome'])."</td>"; }
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
		$sql = "insert into ".$this->tabela.
		"(nome) ".
		"values ".
		"( '".utf8_encode($model->nome)."' )";
		$result = mysql_query( $sql );		
		if( !$result ) {
		    return false;
		}	
		return true;		
	}
	
	function atualizar($model){		
		$sql = "update ".$this->tabela." set ".
		"nome = '".utf8_encode($model->nome)."' ".
		"where idModelo = ".$model->idModelo;
		$result = mysql_query( $sql );		
		if( !$result ) {
		    return false;
		}	
		return true;		
	}

	function apagar($id){
		$sql = "delete from ".$this->tabela." where idModelo = ".$id." ";
		$result = mysql_query( $sql );		
		if( !$result ) {
		    return false;
		}	
		return true;		
	}
	
}