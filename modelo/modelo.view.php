<?php
session_start();
include ("config.php");

isset($_REQUEST['acao']) ? $acao = $_REQUEST['acao'] : $acao = null;
isset($_REQUEST['id']) ? $id = $_REQUEST['id'] : $id = null;

if ($acao=='pesquisa') {
	include_once ($Aplicativo.".control.php");
	$control = new ModeloControl();
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
	if ($id != null) { 
		include_once ($Aplicativo.".model.php");
		$model = new Modelo();
		$model->setModelo($id);
		//echo $model;
		
		include_once ($Aplicativo.".control.php");
		$control = new ModeloControl();		
	}
?>
<html lang="pt-br"><head><meta http-equiv="Content-Type"  content="text/html; charset=iso-8859-1" /> </head><body>
<fieldset>
<input type="button" value="Grava" onclick="grava();lista()"> <input type="button" value="Cancela" onclick="lista()"><br />
<hr />
  <!--<legend>Login:</legend>-->
<label for="lcodigo">Código </label><input type="hidden" value="<?php if (isset($model)) {echo $model->getIdModelo();} ?>" id="idModelo"><?php if (isset($model)) {echo $model->getIdModelo();} ?><br />
<label for="lnome">Nome </label><input type="text" value="<?php if (isset($model)) {echo $model->getNome();} ?>" id="nome" style="width:400px;"><br />

<div id="divPergunta">
<?php
if (isset($model)) { 
	echo $control->getListaPergunta($model->getIdModelo());
}
?>
</div>

</fieldset>
</body></html>
<?php
if (isset($model)) { unset($model); } 
}
?>
