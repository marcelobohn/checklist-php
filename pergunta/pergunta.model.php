<?php
include ("config.php");
include_once ("../conexaoBD.php");

class Pergunta
{

	/* Conexão com o banco de dados */
	var $bd;
	
	//construtor
	function Pergunta(){
		$this->bd = new conexaoBD();		
		
		$this->idPergunta = 0;
		$this->descricao = "";		
	}

	/*
	* Propriedades
	*/
	var $idPergunta;
	var $descricao;
	var $marcar;
	var $resposta;
	
	/* Métodos get e set das propriedade */
	function setIdPergunta( $idPergunta )
	{
		$this->idPergunta = $idPergunta;
	}
	function getIdPergunta()
	{
		return $this->idPergunta;
	}
	
	function setDescricao( $descricao )
	{
		$this->descricao = $descricao;
	}
	function getDescricao()
	{
		return $this->descricao;
	}
	
	function setMarcar( $marcar )
	{
		$this->marcar = $marcar;
	}
	function getMarcar()
	{
		return $this->marcar;
	}
	
	function setResposta( $resposta )
	{
		$this->resposta = $resposta;
	}
	function getResposta()
	{
		return $this->resposta;
	}
		
	function setPergunta($idPergunta) {
		$sql = "select * from pergunta ";
		$sql .= "  where idPergunta = ".$idPergunta."";	
		$result = mysql_query( $sql );
		$registros = mysql_num_rows( $result );
		if( $registros > 0 )	{
			while( $r = mysql_fetch_array( $result ) ){
				$this->idPergunta = $r['idPergunta'];
				$this->descricao = utf8_decode($r['descricao']);
				$this->marcar = $r['marcar'];
				$this->resposta = $r['resposta'];
			}
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
	$control = new PerguntaControl();
	$control->apagar($id);	
	echo "Excluído com sucesso";
	unset($control);
}

if ($_REQUEST['acao']=='grava') {
	header("Content-Type: text/html; charset=ISO-8859-1",true);
	include_once ($Aplicativo.".control.php");
	
	$model = new Pergunta();
	$control = new PerguntaControl();
	
	$model->setIdPergunta($_REQUEST['idPergunta']);
	//$model->crossUrlDecode(setDescricao($_REQUEST['descricao']));
	$model->setDescricao($_REQUEST['descricao']);	
	$model->setMarcar($_REQUEST['marcar']);
	$model->setResposta($_REQUEST['resposta']);

	/*if (!$bd->status_online) {
	echo "descricao: ".htmlspecialchars(urldecode($_REQUEST['descricao']))."<br />";
	echo "descricao: ".htmlspecialchars($_REQUEST['descricao'])."<br />";
	echo "descricao: ".urldecode($_REQUEST['descricao'])."<br />";
	echo "descricao: ".crossUrlDecode($_REQUEST['descricao'])."<br />";
	//echo "descricao: ".to_utf8($_REQUEST['descricao'])."<br />";
	}*/
	
	if ($model->idPergunta != 0) {	
		$control->atualizar($model);
	} else {
		$control->inserir($model);	
	}
	//sleep(1/10);
	unset($control);
	unset($model);
}

?>