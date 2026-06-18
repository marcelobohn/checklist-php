<?php
require_once __DIR__ . '/../block.php';

use App\Modelo;
use App\ModeloControl;
use App\Dispatcher;

(new Dispatcher())
	->on('grava', function () {
		require_once __DIR__ . '/../csrf.php';

		$model = new Modelo();
		$model->setIdModelo($_REQUEST['idModelo']);
		$model->setNome($_REQUEST['nome']);

		$control = new ModeloControl();
		if ((int)$model->idModelo > 0) {
			$control->atualizar($model);
		} else {
			$control->inserir($model);
		}
	})
	->on('apaga', function () {
		require_once __DIR__ . '/../csrf.php';

		$control = new ModeloControl();
		if ($control->apagar($_REQUEST['id'])) {
			echo "Excluído com sucesso";
		} else {
			echo "Não foi possível excluir: modelo com checklists respondidos.";
		}
	})
	->run($_REQUEST['acao'] ?? null);
