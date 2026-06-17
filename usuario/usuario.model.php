<?php require_once(__DIR__ . "/../block.php"); ?>
<?php
include_once ("config.php");
include_once ("../conexaoBD.php");

class Usuario
{

	/* Conexão com o banco de dados */
	var $bd;

	//construtor
	function __construct(){
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
		$sql = "select * from ".$this->tabela." where idUsuario = ?";
		$r = $this->bd->query( $sql, array( $idUsuario ) )->fetch();
		if( $r )	{
			$this->idUsuario = $r['idUsuario'];
			$this->nome = $r['nome'];
			$this->senha = $r['senha'];
			$this->admin = $r['admin'];
			$retorno = true;
		}
		else
		$retorno = false;
		return $retorno;
	}
}

if (($_REQUEST['acao'] ?? '')=='apaga') {
	require_once(__DIR__ . "/../csrf.php");
	include_once ($Aplicativo.".control.php");
	$id = $_REQUEST['id'];
	$control = new UsuarioControl();
	$control->apagar($id);
	echo "Excluído com sucesso";
	unset($control);
}

if (($_REQUEST['acao'] ?? '')=='grava') {
	require_once(__DIR__ . "/../csrf.php");
	include_once ($Aplicativo.".control.php");

	$model = new Usuario();
	$control = new UsuarioControl();

	$model->setIdUsuario($_REQUEST['idUsuario']);
	$model->setNome($_REQUEST['nome']);
	$model->setSenha($_REQUEST['senha']);
	$model->setAdmin($_REQUEST['admin']);

	if ((int)$model->idUsuario > 0) {
		$control->atualizar($model);
	} else {
		$control->inserir($model);
	}

	unset($control);
	unset($model);
}

?>