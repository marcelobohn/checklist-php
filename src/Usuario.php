<?php

declare(strict_types=1);

namespace App;

class Usuario
{
	/* Conexão com o banco de dados */
	var $bd;

	//construtor
	function __construct(){
		$this->bd = new ConexaoBD();
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
	function setIdUsuario( $idUsuario ): void
	{
		$this->idUsuario = $idUsuario;
	}
	function getIdUsuario()
	{
		return $this->idUsuario;
	}

	function setNome( $nome ): void
	{
		$this->nome = $nome;
	}
	function getNome()
	{
		return $this->nome;
	}

	function setSenha( $senha ): void
	{
		$this->senha = $senha;
	}
	function getSenha()
	{
		return $this->senha;
	}

	function setAdmin( $admin ): void
	{
		$this->admin = $admin;
	}
	function getAdmin()
	{
		return $this->admin;
	}

	function setUsuario($idUsuario): bool {
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
