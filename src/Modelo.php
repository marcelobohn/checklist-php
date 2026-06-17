<?php

namespace App;

class Modelo
{
	/* Conexão com o banco de dados */
	var $bd;

	//construtor
	function __construct(){
		$this->bd = new ConexaoBD();
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
