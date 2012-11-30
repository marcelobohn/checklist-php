<?php
session_start();
include ("config.php");

isset($_REQUEST['acao']) ? $acao = $_REQUEST['acao'] : $acao = null;
isset($_REQUEST['id']) ? $id = $_REQUEST['id'] : $id = null;

if ($acao=='pesquisa') {
	include_once ($Aplicativo.".control.php");
	//require_once ($Aplicativo.'.control.php');
	$control = new PerguntaControl(); 
	isset($_REQUEST['p']) ? $p = $_REQUEST['p'] : $p = null;
	isset($_REQUEST['pag']) ? $pagina = $_REQUEST['pag'] : $pagina = null;
	if ($pagina == 'undefined') {
		$pagina = 1;
	}
?>
<html lang="pt-br">
	<head>
	<meta http-equiv="Content-Type"  content="text/html; charset=iso-8859-1" /> 
	</head>
	<body>
	<?php echo $control->getLista($p,$pagina) ?>
	</body>
</html>
<?php
	unset($control);
}
?>


<?php
if ($acao=='form') { 
	//header("Content-Type: text/html; charset=ISO-8859-1",true);
	//header('Content-Type: text/html; charset=utf-8');
	if ($id != null) {
		$arq = $Aplicativo.".model.php";
		include_once ("pergunta.model.php");
		//require_once 'pergunta.model.php';
		$model = new Pergunta();
		$model->setPergunta($id);
		
		include_once ($Aplicativo.".control.php");
		$control = new PerguntaControl();				
	}
?>
<html lang="pt-br"><head><meta http-equiv="Content-Type"  content="text/html; charset=iso-8859-1" /> </head><body>
<fieldset>
<input type="button" value="Grava" onclick="grava();lista();"> <input type="button" value="Cancela" onclick="lista()"><br />
<hr />
  <!--<legend>Login:</legend>-->
<label for="codigo">Código </label><input type="hidden" value="<?php if (isset($model)) {echo $model->getIdPergunta();} ?>" id="idPergunta" style="width:100px;"><?php if (isset($model)) {echo $model->getIdPergunta();} ?><br />
<label for="nome">Descricao </label><input type="text" value="<?php if (isset($model)) {echo $model->getDescricao();} ?>" id="descricao" style="width:400px;"><br />
<label for="cidade">Marcar </label><input type="checkbox" <?php if (isset($model)) { if ($model->getMarcar()=="S") { echo "checked=\"checked\"";}} ?> id="marcar"><br />
<label for="site">Resposta </label><input type="checkbox" <?php if (isset($model)) { if ($model->getResposta()=="S") { echo "checked=\"checked\"";}} ?> id="resposta" onclick="listaResposta(resposta.checked)"><br />

<div id="divResposta">
<?php
if (isset($model)) { 
	if ($model->getResposta()=="S") { 
		//include("pergunta.resposta.lista.php");
		echo $control->getListaResposta($model->getIdPergunta());
	}
}
?>
</div>
</fieldset>
</body></html>
<?php
if (isset($model)) { unset($model); } 
if (isset($control)) { unset($control); }
 
}
?>
