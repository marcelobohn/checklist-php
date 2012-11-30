<?php
session_start();

include ("config.php");

isset($_REQUEST['acao']) ? $acao = $_REQUEST['acao'] : $acao = null;
isset($_REQUEST['id']) ? $id = $_REQUEST['id'] : $id = null;

if ($acao=='pesquisa') {
	include_once ($Aplicativo.".control.php");
	$control = new UsuarioControl();
	isset($_REQUEST['p']) ? $p = $_REQUEST['p'] : $p = null;
	isset($_REQUEST['pag']) ? $pagina = $_REQUEST['pag'] : $pagina = null;
	if ($pagina == 'undefined') {
		$pagina = 1;
	}
	?>
<html lang="pt-br">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
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
		$model = new Usuario();
		$model->setUsuario($id);

		include_once ($Aplicativo.".control.php");
		$control = new UsuarioControl();
	}
	?>
<html lang="pt-br">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body>
	<fieldset>
		<input type="button" value="Grava" onclick="grava();lista()"> <input
			type="button" value="Cancela" onclick="lista()"><br />
		<hr />
		<!--<legend>Login:</legend>-->
		<label for="lcodigo">Código </label>
			<input type="hidden" value="<?php if (isset($model)) {echo $model->getIdUsuario();} ?>" id="idUsuario"> <?php if (isset($model)) {echo $model->getIdUsuario();} ?><br /> 
		<label for="lnome">Nome </label>
			<input type="text" value="<?php if (isset($model)) {echo $model->getNome();} ?>" id="nome" style="width: 400px;"><br /> 
		<label for="lsenha">Senha </label>
			<input type="password" value="<?php if (isset($model)) {echo $model->getSenha();} ?>" id="senha" style="width: 400px;"><br /> 
		<label for="ladmin">Administrador</label>
			<input type="checkbox" <?php if (isset($model)) { if ($model->getAdmin()=="S") { echo "checked=\"checked\"";}} ?> id="admin"><br />
	</fieldset>
</body>
</html>
		<?php
		if (isset($model)) { unset($model); }
}
?>
