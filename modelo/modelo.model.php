<?php
require_once __DIR__ . '/../block.php';

use App\Modelo;
use App\ModeloControl;

if (($_REQUEST['acao'] ?? '')=='apaga') {
	require_once(__DIR__ . "/../csrf.php");
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
