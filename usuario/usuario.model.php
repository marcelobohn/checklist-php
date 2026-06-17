<?php
require_once __DIR__ . '/../block.php';

use App\Usuario;
use App\UsuarioControl;

if (($_REQUEST['acao'] ?? '')=='apaga') {
	require_once(__DIR__ . "/../csrf.php");
	$id = $_REQUEST['id'];
	$control = new UsuarioControl();
	$control->apagar($id);
	echo "Excluído com sucesso";
	unset($control);
}

if (($_REQUEST['acao'] ?? '')=='grava') {
	require_once(__DIR__ . "/../csrf.php");

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
