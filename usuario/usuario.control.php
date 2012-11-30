<?php
include_once ("../conexaoBD.php");

class UsuarioControl {

	/* Conexão com o banco de dados */
	var $bd;
	var $tabela = "usuario";

	//construtor
	function UsuarioControl(){
		$this->bd = new conexaoBD();
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
			"<th style=\"width:200px;\">Nome</th>".
			"<th>Admin</th>".
			"</tr>";
			// style=\" border-bottom: 1px solid black;\"
			while( $r = mysql_fetch_array( $result ) ){
				$resposta = $resposta .
				"\n<tr> " . 
				"<td><input type=\"checkbox\" value=\"".$r[0]."\"></td>" . 
				"<td>".$r[0]."</td>"; 
				if ($_SESSION['modo']=='de') { $resposta .=
					"<td><a href=\"javascript:altera(".$r[0].");\">".utf8_decode($r['nome'])."</a></td>"; } 
				else { $resposta .=
					"<td>".utf8_decode($r['nome'])."</td>"; }
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
		$sql = "insert into ".$this->tabela.
		"(nome, senha, admin) ".
		"values ".
		"( '".utf8_encode($model->nome)."', '".utf8_encode($model->senha)."', '".$model->admin."' )";
		$result = mysql_query( $sql );
		if( !$result ) {
			return false;
		}
		return true;
	}

	function atualizar($model){
		$sql = "update ".$this->tabela." set ".
		"nome = '".utf8_encode($model->nome)."', ".
		"senha = '".utf8_encode($model->senha)."', ".		
		"admin = '".$model->admin."' ".		
		"where idUsuario = ".$model->idUsuario;
		$result = mysql_query( $sql );
		if( !$result ) {
			return false;
		}
		return true;
	}

	function apagar($id){
		$sql = "delete from ".$this->tabela." where idUsuario = ".$id." ";
		$result = mysql_query( $sql );
		if( !$result ) {
			return false;
		}
		return true;
	}

}