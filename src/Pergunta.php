<?php

declare(strict_types=1);

namespace App;

class Pergunta
{
	/* Conexão com o banco de dados */
	var $bd;

	//construtor
	function __construct(){
		$this->bd = new ConexaoBD();

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
	function setIdPergunta( $idPergunta ): void
	{
		$this->idPergunta = $idPergunta;
	}
	function getIdPergunta()
	{
		return $this->idPergunta;
	}

	function setDescricao( $descricao ): void
	{
		$this->descricao = $descricao;
	}
	function getDescricao()
	{
		return $this->descricao;
	}

	function setMarcar( $marcar ): void
	{
		$this->marcar = $marcar;
	}
	function getMarcar()
	{
		return $this->marcar;
	}

	function setResposta( $resposta ): void
	{
		$this->resposta = $resposta;
	}
	function getResposta()
	{
		return $this->resposta;
	}

	function setPergunta($idPergunta): bool {
		$sql = "select * from pergunta where idPergunta = ?";
		$r = $this->bd->query( $sql, array( $idPergunta ) )->fetch();
		if( $r )	{
			$this->idPergunta = $r['idPergunta'];
			$this->descricao = $r['descricao'];
			$this->marcar = $r['marcar'];
			$this->resposta = $r['resposta'];
			$retorno = true;
		}
		else
			$retorno = false;
		return $retorno;
	}
}
