<?php
include_once ("../conexaoBD.php");

class ModeloControl {

	/* Conexão com o banco de dados */
	var $bd;
	var $tabela = "modelo";
	
	//construtor
	function __construct(){
		$this->bd = new conexaoBD();
	}

	function getListaPergunta($id) {
	$resposta = "<br />";
	$sql = "select * from pergunta p where p.idpergunta not in (select idpergunta from modelopergunta mp where mp.idModelo = ?)";
	$rows = $this->bd->query( $sql, array( $id ) )->fetchAll();
	if( count($rows) > 0 )	{
		$resposta .= "Perguntas diponíveis: <br /><select id=\"idPergunta\">";
		foreach( $rows as $r ){
			$resposta .= "<option value=".$r['idPergunta']." >".h($r['descricao'])."</option>";
		}
		$resposta .= "</select>";
		$resposta .=  "<a href=\"javascript:incluiPergunta(idModelo.value, idPergunta.value);listaPergunta()\">Inclui pergunta</a><br />";
	}
	$resposta .= "Perguntas selecionadas: <ul style=\"list-style-type: none;\">";
	$sql = "select mp.*, p.descricao from modelopergunta mp";
	$sql .= "  join pergunta p on mp.idPergunta = p.idPergunta";
	$sql .= "  where idModelo = ? order by ordem";
	$rows = $this->bd->query( $sql, array( $id ) )->fetchAll();
	if( count($rows) > 0 )	{
		$recNo = 0;
		foreach( $rows as $r ){
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
			$resposta .= "".h($r['descricao'])."</li>";
		}
	}
	echo "</ul>";
	return $resposta;
	}

	function getLista($p,$pag) {
		//header("Content-Type: text/html; charset=UTF-8",true);
		$resposta = "";
		$where = " where 1=1 ";
		$params = array();
		if ($p != null) { $where .= "  and nome like ?"; $params[] = $p."%"; }

		$totalReg = $this->bd->query("select count(*) from ".$this->tabela.$where, $params)->fetchColumn();
		$itensPagina = 10;
		$ini = ($pag - 1) * $itensPagina;

		$sql = "select * from ".$this->tabela.$where." order by nome LIMIT ".(int)$ini.", ".(int)$itensPagina;

		$totalPaginas = ceil($totalReg/$itensPagina);
		for ($i = 1; $i <= $totalPaginas; $i++) {
			if ($pag != $i) { $resposta .= "<a href=\"javascript:lista(".$i.")\">".$i."</a>"; } 
			else { $resposta .= $i; }
			if ($i != $totalPaginas) { $resposta .= " - "; }
		}
		$resposta .= " | Registros: ".$totalReg;		
		
		$rows = $this->bd->query( $sql, $params )->fetchAll();
		if( count($rows) > 0 )	{
			$resposta .= "<table border='0'><tr>".
			"<th></th>".
			"<th>Código</th>".
			"<th>Nome</th>".
			"</tr>";
			foreach( $rows as $r ){
				$resposta = $resposta .
				"\n<tr> " .
				"<td><input type=\"checkbox\" value=\"".$r[0]."\"></td>" .
				"<td>".$r[0]."</td>";
				if (($_SESSION['perfil'] ?? '')=='adm') { $resposta .=
					"<td><a href=\"javascript:altera(".$r[0].");\">".h($r['nome'])."</a></td>"; }
				else { $resposta .=
					"<td>".h($r['nome'])."</td>"; }
				$resposta .=
				"<td>".($r['marcar'] ?? '')."</td>" .
				"<td>".($r['resposta'] ?? '')."</td>" .
				"</tr>";
			}
			$resposta = $resposta . "</table>\n";

		}
		else
			$resposta = "Não foi encontrado nenhum dado.";
		return $resposta;
	}

	function inserir($model){
		$sql = "insert into ".$this->tabela." (nome) values ( ? )";
		$this->bd->query( $sql, array( $model->nome ) );
		return true;
	}

	function atualizar($model){
		$sql = "update ".$this->tabela." set nome = ? where idModelo = ?";
		$this->bd->query( $sql, array( $model->nome, $model->idModelo ) );
		return true;
	}

	function apagar($id){
		$sql = "delete from ".$this->tabela." where idModelo = ?";
		$this->bd->query( $sql, array( $id ) );
		return true;
	}
	
}