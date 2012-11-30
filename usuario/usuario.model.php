<?php
include_once ("config.php");
include_once ("../conexaoBD.php");

class Usuario
{

	/* Conexão com o banco de dados */
	var $bd;

	//construtor
	function Usuario(){
		$this->bd = new conexaoBD();
		$this->idUsuario = 0;
		$this->nome = "";
		$this->admin = "N";
	}

	/*
	 * Propriedades
	 */
	var $idUsuario;
	var $nome;
	var $senha;
	var $admin;
	var $tabela = "usuario";

	/* Métodos get e set das propriedade */
	function setIdUsuario( $idUsuario )
	{
		$this->idUsuario = $idUsuario;
	}
	function getIdUsuario()
	{
		return $this->idUsuario;
	}

	function setNome( $nome )
	{
		$this->nome = $nome;
	}
	function getNome()
	{
		return $this->nome;
	}

	function setSenha( $senha )
	{
		$this->senha = $senha;
	}
	function getSenha()
	{
		return $this->senha;
	}

	function setAdmin( $admin )
	{
		$this->admin = $admin;
	}
	function getAdmin()
	{
		return $this->admin;
	}

	function setUsuario($idUsuario) {
		$sql = "select * from ".$this->tabela;
		$sql .= "  where idUsuario = ".$idUsuario."";
		$result = mysql_query( $sql );
		$registros = mysql_num_rows( $result );
		if( $registros > 0 )	{
			$r = mysql_fetch_assoc( $result );
			$this->idUsuario = $r['idUsuario'];
			$this->nome = utf8_decode($r['nome']);
			$this->senha = utf8_decode($r['senha']);
			$this->admin = utf8_decode($r['admin']);
			$retorno = true;
		}
		else
		$retorno = false;
		return $retorno;
	}
}

if ($_REQUEST['acao']=='apaga') {
	include_once ($Aplicativo.".control.php");
	$id = $_REQUEST['id'];
	$control = new UsuarioControl();
	$control->apagar($id);
	echo "Excluído com sucesso";
	unset($control);
}

if ($_REQUEST['acao']=='grava') {
	include_once ($Aplicativo.".control.php");

	$model = new Usuario();
	$control = new UsuarioControl();

	$model->setIdUsuario($_REQUEST['idUsuario']);
	$model->setNome($_REQUEST['nome']);
	$model->setSenha($_REQUEST['senha']);
	$model->setAdmin($_REQUEST['admin']);

	if ($model->idUsuario != 0) {
		$control->atualizar($model);
	} else {
		$control->inserir($model);
	}

	unset($control);
	unset($model);
}

?>