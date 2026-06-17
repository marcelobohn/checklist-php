<?php

namespace App;

class PerguntaControl {

	/* Conexão com o banco de dados */
	var $bd;

	//construtor
	function __construct(){
		$this->bd = new ConexaoBD();
	}

	function getListaResposta($id) {
		$resposta = "";
		$resposta .=  "<span style=\"font-size:16px; float:left;\">Respostas</span> &nbsp;&nbsp;&nbsp; ";
		$resposta .=  "<a href=\"javascript:incluiResposta(idPergunta.value)\">Inclui resposta</a><br />";
		$resposta .= "<ul>";
		$sql = "select * from resposta where idPergunta = ?";
		$rows = $this->bd->query( $sql, array( $id ) )->fetchAll();

		foreach( $rows as $r ){
			$resposta .= "<li>".h($r['descricao'])."&nbsp;&nbsp;<a href=\"javascript:apagaResposta(".$id.",".$r['idResposta'].")\"><b>X</b></a> </li>";
		}
		echo "</ul>";
		return $resposta;
	}

	function getLista($p,$pag) {
		//header("Content-Type: text/html; charset=UTF-8",true);
		//header('Content-Type: text/html; charset=utf-8');
		$resposta = "";
		$where = " where 1=1 ";
		$params = array();
		if ($p != null) { $where .= "  and descricao like ?"; $params[] = $p."%"; }

		$totalReg = $this->bd->query("select count(*) from pergunta".$where, $params)->fetchColumn();
		$itensPagina = 10;
		$ini = ($pag - 1) * $itensPagina;

		$sql = "select * from pergunta".$where." order by descricao LIMIT ".(int)$ini.", ".(int)$itensPagina;

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
			"<th>C&oacute;digo</th>".
			"<th>Descricao</th>".
			"<th>Marca</th>".
			"<th>Resposta</th>".
			"</tr>";
			foreach( $rows as $r ){
				$resposta = $resposta .
				"\n<tr> " .
				"<td><input type=\"checkbox\" value=\"".$r[0]."\"></td>" .
				"<td>".$r[0]."</td>";
				if (($_SESSION['perfil'] ?? '')=='adm') { $resposta .=
					"<td><a href=\"javascript:altera(".$r[0].");\">".h($r['descricao'])."</a></td>"; }
				else { $resposta .=
					"<td>".h($r['descricao'])."</td>"; }
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
		$sql = "insert into pergunta (descricao, marcar, resposta) values ( ?, ?, ? )";
		$this->bd->query( $sql, array( $model->descricao, $model->marcar, $model->resposta ) );
		return true;
	}

	function atualizar($model){
		$sql = "update pergunta set descricao = ?, marcar = ?, resposta = ? where idPergunta = ?";
		$this->bd->query( $sql, array( $model->descricao, $model->marcar, $model->resposta, $model->idPergunta ) );
		return true;
	}

	function apagar($id){
		// FK RESTRICT (registroitem->pergunta): pergunta usada em checklist
		// respondido não pode ser apagada. Suas respostas e associações de
		// modelo (modelopergunta) somem em cascata.
		$sql = "delete from pergunta where idPergunta = ?";
		try {
			$this->bd->query( $sql, array( $id ) );
			return true;
		} catch (\PDOException $e) {
			return false;
		}
	}

}
