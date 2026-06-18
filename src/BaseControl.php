<?php

declare(strict_types=1);

namespace App;

/**
 * Base dos controllers de CRUD com listagem paginada.
 *
 * Reúne o esqueleto comum do getLista (filtro + count + paginação + rodapé +
 * fetch); cada subclasse define a tabela e a coluna de busca/ordenação e
 * implementa renderTabela() com seus próprios cabeçalhos/células (template
 * method).
 */
abstract class BaseControl
{
	/* Conexão com o banco de dados */
	var $bd;

	/** Tabela do módulo (subclasse define). */
	var $tabela;

	/** Coluna usada no filtro de busca e na ordenação. */
	protected $colunaBusca = 'nome';

	function __construct(){
		$this->bd = new ConexaoBD();
	}

	function getLista($p, $pag): string {
		$resposta = "";
		$where = " where 1=1 ";
		$params = array();
		if ($p != null) { $where .= "  and ".$this->colunaBusca." like ?"; $params[] = $p."%"; }

		$totalReg = $this->bd->query("select count(*) from ".$this->tabela.$where, $params)->fetchColumn();
		$itensPagina = 10;
		$ini = ($pag - 1) * $itensPagina;

		$sql = "select * from ".$this->tabela.$where." order by ".$this->colunaBusca." LIMIT ".(int)$ini.", ".(int)$itensPagina;

		$totalPaginas = ceil($totalReg/$itensPagina);
		for ($i = 1; $i <= $totalPaginas; $i++) {
			if ($pag != $i) { $resposta .= "<a href=\"javascript:lista(".$i.")\">".$i."</a>"; }
			else { $resposta .= $i; }
			if ($i != $totalPaginas) { $resposta .= " - "; }
		}
		$resposta .= " | Registros: ".$totalReg;

		$rows = $this->bd->query( $sql, $params )->fetchAll();
		if( count($rows) > 0 )	{
			$resposta .= $this->renderTabela($rows);
		}
		else
			$resposta = "Não foi encontrado nenhum dado.";
		return $resposta;
	}

	/** Renderiza a tabela HTML da listagem (cabeçalhos + linhas). */
	abstract protected function renderTabela(array $rows): string;
}
