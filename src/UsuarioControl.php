<?php

declare(strict_types=1);

namespace App;

class UsuarioControl extends BaseControl {

	var $tabela = "usuario";
	protected $colunaBusca = "nome";

	protected function renderTabela(array $rows): string {
		$resposta = "<table border='0'><tr>".
		"<th></th>".
		"<th>Código</th>".
		"<th style=\"width:200px;\">Nome</th>".
		"<th>Admin</th>".
		"</tr>";
		foreach( $rows as $r ){
			$resposta = $resposta .
			"\n<tr> " .
			"<td><input type=\"checkbox\" value=\"".$r[0]."\"></td>" .
			"<td>".$r[0]."</td>";
			if (($_SESSION['modo'] ?? '')=='de') { $resposta .=
				"<td><a href=\"javascript:altera(".$r[0].");\">".h($r['nome'])."</a></td>"; }
			else { $resposta .=
				"<td>".h($r['nome'])."</td>"; }
			$resposta .=
				"<td>".$r['admin']."</td>";
			$resposta .=
			"</tr>";
		}
		$resposta = $resposta . "</table>\n";
		return $resposta;
	}

	function inserir($model): bool {
		$senhaHash = password_hash( $model->senha, PASSWORD_DEFAULT );
		$sql = "insert into ".$this->tabela." (nome, senha, admin) values ( ?, ?, ? )";
		$this->bd->query( $sql, array( $model->nome, $senhaHash, $model->admin ) );
		return true;
	}

	function atualizar($model): bool {
		// Só atualiza a senha quando uma nova é informada; vazia mantém a atual.
		if ($model->senha !== null && $model->senha !== "") {
			$senhaHash = password_hash( $model->senha, PASSWORD_DEFAULT );
			$sql = "update ".$this->tabela." set nome = ?, senha = ?, admin = ? where idUsuario = ?";
			$this->bd->query( $sql, array( $model->nome, $senhaHash, $model->admin, $model->idUsuario ) );
		} else {
			$sql = "update ".$this->tabela." set nome = ?, admin = ? where idUsuario = ?";
			$this->bd->query( $sql, array( $model->nome, $model->admin, $model->idUsuario ) );
		}
		return true;
	}

	function apagar($id): bool {
		$sql = "delete from ".$this->tabela." where idUsuario = ?";
		$this->bd->query( $sql, array( $id ) );
		return true;
	}

}
