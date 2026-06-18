<?php

declare(strict_types=1);

namespace App;

class ModeloControl extends BaseControl {

	var $tabela = "modelo";
	protected $colunaBusca = "nome";

	function getListaPergunta($id): string {
	$resposta = "<br />";
	$sql = "select * from pergunta p where p.idpergunta not in (select idpergunta from modelopergunta mp where mp.idModelo = ?)";
	$rows = $this->bd->query( $sql, array( $id ) )->fetchAll();
	if( count($rows) > 0 )	{
		$resposta .= "Perguntas diponíveis: <br /><select id=\"idPergunta\">";
		foreach( $rows as $r ){
			$resposta .= "<option value=".$r['idPergunta']." >".h($r['descricao'])."</option>";
		}
		$resposta .= "</select>";
		$resposta .=  "<a href=\"javascript:incluiPergunta(idModelo.value, idPergunta.value)\">Inclui pergunta</a><br />";
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
			$resposta .= "<a href=\"javascript:apagaPergunta(".$id.",".$r['idPergunta'].")\"><b>X</b></a>&nbsp;&nbsp;";
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

	protected function renderTabela(array $rows): string {
		$resposta = "<table border='0'><tr>".
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
		return $resposta;
	}

	function inserir($model): bool {
		$sql = "insert into ".$this->tabela." (nome) values ( ? )";
		$this->bd->query( $sql, array( $model->nome ) );
		return true;
	}

	function atualizar($model): bool {
		$sql = "update ".$this->tabela." set nome = ? where idModelo = ?";
		$this->bd->query( $sql, array( $model->nome, $model->idModelo ) );
		return true;
	}

	function apagar($id): bool {
		// FK RESTRICT (registro->modelo): modelo com checklist respondido não
		// pode ser apagado. Suas associações (modelopergunta) somem em cascata.
		$sql = "delete from ".$this->tabela." where idModelo = ?";
		try {
			$this->bd->query( $sql, array( $id ) );
			return true;
		} catch (\PDOException $e) {
			return false;
		}
	}

}
