<?php
include_once ("../conexaoBD.php");

class UsuarioControl {

	/* Conexão com o banco de dados */
	var $bd;
	var $tabela = "usuario";

	//construtor
	function __construct(){
		$this->bd = new conexaoBD();
	}

	function getLista($p,$pag) {
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
			"<th style=\"width:200px;\">Nome</th>".
			"<th>Admin</th>".
			"</tr>";
			foreach( $rows as $r ){
				$resposta = $resposta .
				"\n<tr> " .
				"<td><input type=\"checkbox\" value=\"".$r[0]."\"></td>" .
				"<td>".$r[0]."</td>";
				if (($_SESSION['modo'] ?? '')=='de') { $resposta .=
					"<td><a href=\"javascript:altera(".$r[0].");\">".$r['nome']."</a></td>"; }
				else { $resposta .=
					"<td>".$r['nome']."</td>"; }
				$resposta .=
					"<td>".$r['admin']."</td>";
				$resposta .=
				"</tr>";
			}
			$resposta = $resposta . "</table>\n";

		}
		else
		$resposta = "Não foi encontrado nenhum dado.";
		return $resposta;
	}

	function inserir($model){
		$sql = "insert into ".$this->tabela." (nome, senha, admin) values ( ?, ?, ? )";
		$this->bd->query( $sql, array( $model->nome, $model->senha, $model->admin ) );
		return true;
	}

	function atualizar($model){
		$sql = "update ".$this->tabela." set nome = ?, senha = ?, admin = ? where idUsuario = ?";
		$this->bd->query( $sql, array( $model->nome, $model->senha, $model->admin, $model->idUsuario ) );
		return true;
	}

	function apagar($id){
		$sql = "delete from ".$this->tabela." where idUsuario = ?";
		$this->bd->query( $sql, array( $id ) );
		return true;
	}

}