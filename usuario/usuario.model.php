<?php
require_once __DIR__ . '/../block.php';

use App\Usuario;
use App\UsuarioControl;
use App\Dispatcher;

(new Dispatcher())
	->on('grava', function () {
		require_once __DIR__ . '/../csrf.php';

		$model = new Usuario();
		$model->setIdUsuario($_REQUEST['idUsuario']);
		$model->setNome($_REQUEST['nome']);
		$model->setSenha($_REQUEST['senha']);
		$model->setAdmin($_REQUEST['admin']);

		$control = new UsuarioControl();
		if ((int)$model->idUsuario > 0) {
			$control->atualizar($model);
		} else {
			$control->inserir($model);
		}
	})
	->on('apaga', function () {
		require_once __DIR__ . '/../csrf.php';

		$control = new UsuarioControl();
		$control->apagar($_REQUEST['id']);
		echo "Excluído com sucesso";
	})
	->run($_REQUEST['acao'] ?? null);
