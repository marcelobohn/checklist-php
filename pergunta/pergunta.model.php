<?php
require_once __DIR__ . '/../block.php';

use App\Pergunta;
use App\PerguntaControl;

if (($_REQUEST['acao'] ?? '')=='apaga') {
	require_once(__DIR__ . "/../csrf.php");
	$id = $_REQUEST['id'];
	$control = new PerguntaControl();
	if ($control->apagar($id)) {
		echo "Excluído com sucesso";
	} else {
		echo "Não foi possível excluir: pergunta em uso em checklists respondidos.";
	}
	unset($control);
}

if (($_REQUEST['acao'] ?? '')=='grava') {
	require_once(__DIR__ . "/../csrf.php");
	header("Content-Type: text/html; charset=UTF-8",true);

	$model = new Pergunta();
	$control = new PerguntaControl();

	$model->setIdPergunta($_REQUEST['idPergunta']);
	$model->setDescricao($_REQUEST['descricao']);
	$model->setMarcar($_REQUEST['marcar']);
	$model->setResposta($_REQUEST['resposta']);

	if ((int)$model->idPergunta > 0) {
		$control->atualizar($model);
	} else {
		$control->inserir($model);
	}

	unset($control);
	unset($model);
}
