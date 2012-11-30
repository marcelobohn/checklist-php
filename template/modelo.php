<?php require_once("../block.php"); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="pt-br">
	<head>
		<title>{Titulo}</title>
		<meta http-equiv="content-language" content="pt-br" /> 
		<meta http-equiv="Content-Type"  content="text/html; charset=iso-8859-1" /> 
		{IntoHead}
	</head>
	<body>
	<div id="divPagina">
		<div id="divCorpo">
			<div id="divTitulo">
				<span class="titulo">Checklist - {Titulo}</span><br />
			</div>
			<div id="divMenu">		
				{Menu}
			</div>
			<div id="divLateral">
				{MenuLateral}
			</div>
			<div id="divConteudo">
				{Conteudo}
			</div>
			<div id="divRodape">
				<div id="resp"></div>
			</div>
		</div>
	</div>
	</body>
</html>
