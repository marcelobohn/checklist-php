<?php require_once(__DIR__ . "/../block.php"); ?>
<?php
include_once ("config.php");
include_once ("../conexaoBD.php");

class Modelo
{

	/* Conexão com o banco de dados */
	var $bd;
	
	//construtor
	function __construct(){
		$this->bd = new conexaoBD();
		$this->idModelo = 0;
		$this->nome = "";		
	}

	/*
	* Propriedades
	*/
	var $idModelo;
	var $nome;
	var $tabela = "modelo";
	
	/* Métodos get e set das propriedade */
	function setIdModelo( $idModelo )
	{
		$this->idModelo = $idModelo;
	}
	function getIdModelo()
	{
		return $this->idModelo;
	}
	
	function setNome( $nome )
	{
		$this->nome = $nome;
	}
	function getNome()
	{
		return $this->nome;
	}
	
	function setModelo($idModelo) {
		$sql = "select * from ".$this->tabela." where idModelo = ?";
		$r = $this->bd->query( $sql, array( $idModelo ) )->fetch();
		if( $r )	{
			$this->idModelo = $r['idModelo'];
			$this->nome = $r['nome'];
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
	$control = new ModeloControl();
	if ($control->apagar($id)) {
		echo "Excluído com sucesso";
	} else {
		echo "Não foi possível excluir: modelo com checklists respondidos.";
	}
	unset($control);
}

if (($_REQUEST['acao'] ?? '')=='grava') {
	require_once(__DIR__ . "/../csrf.php");
	include_once ($Aplicativo.".control.php");
	
	$model = new Modelo();
	$control = new ModeloControl();
	
	$model->setIdModelo($_REQUEST['idModelo']);
	$model->setNome($_REQUEST['nome']);

	if ((int)$model->idModelo > 0) {
		$control->atualizar($model);
	} else {
		$control->inserir($model);	
	}

	unset($control);
	unset($model);
}

?>