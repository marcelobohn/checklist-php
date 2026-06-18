<?php
require_once __DIR__ . '/../block.php';

use App\Pergunta;
use App\PerguntaControl;
use App\Dispatcher;

(new Dispatcher())
	->on('grava', function () {
		require_once __DIR__ . '/../csrf.php';
		header("Content-Type: text/html; charset=UTF-8", true);

		$model = new Pergunta();
		$model->setIdPergunta($_REQUEST['idPergunta']);
		$model->setDescricao($_REQUEST['descricao']);
		$model->setMarcar($_REQUEST['marcar']);
		$model->setResposta($_REQUEST['resposta']);

		$control = new PerguntaControl();
		if ((int)$model->idPergunta > 0) {
			$control->atualizar($model);
		} else {
			$control->inserir($model);
		}
	})
	->on('apaga', function () {
		require_once __DIR__ . '/../csrf.php';

		$control = new PerguntaControl();
		if ($control->apagar($_REQUEST['id'])) {
			echo "Excluído com sucesso";
		} else {
			echo "Não foi possível excluir: pergunta em uso em checklists respondidos.";
		}
	})
	->run($_REQUEST['acao'] ?? null);
