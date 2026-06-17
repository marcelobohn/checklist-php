<?php

namespace App;

class ConexaoBD
{
	/** @var \PDO */
	public $con;
	public $status_online = false;

	//** Construtor que abre a conexao (PDO)
	function __construct()
	{
		// Credenciais vem do ambiente; os defaults servem ao ambiente Docker
		// de desenvolvimento. Em producao, defina as variaveis DB_* e nao
		// dependa dos defaults.
		$host = getenv('DB_HOST') ?: 'localhost';
		$port = getenv('DB_PORT') ?: '3306';
		$name = getenv('DB_NAME') ?: 'checklist';
		$user = getenv('DB_USER') ?: 'root';
		$pass = getenv('DB_PASSWORD');
		if ($pass === false) { $pass = '123'; }

		$dsn = "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4";
		try {
			$this->con = new \PDO(
				$dsn,
				$user,
				$pass,
				array(
					\PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
					\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_BOTH, // acesso por indice E por nome
					\PDO::ATTR_EMULATE_PREPARES   => false,
				)
			);
		} catch (\PDOException $e) {
			echo("Erro ao conectar no Bando de Dados.");
			exit;
		}
	}

	/**
	 * Executa uma query usando prepared statement e devolve o PDOStatement
	 * ja executado. Passe os valores em $params para usar placeholders (?).
	 *
	 *   $stmt = $bd->query("select * from usuario where nome = ?", array($nome));
	 *   foreach ($stmt->fetchAll() as $r) { ... }
	 */
	function query($sql, $params = array())
	{
		$stmt = $this->con->prepare($sql);
		$stmt->execute($params);
		return $stmt;
	}

	//** Fecha a conexao
	function fechaBd()
	{
		$this->con = null;
	}

	function chkOnLine($texto)
	{
		if (!$this->status_online) {
			return $texto;
		}
	}

	function limpaTabela($tabela)
	{
		// nome de tabela nao pode ser parametrizado; uso interno/controlado
		$this->con->exec("truncate table " . $tabela);
	}
}
