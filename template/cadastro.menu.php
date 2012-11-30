<?php require_once("../block.php"); ?>
				<input type="button" value="Inclui" onclick="incluir();" <?php if ($_SESSION['perfil']!='adm') { echo "disabled=\"disabled\""; } ?> >
				<input type="button" value="Exclui" onclick="excluir();" <?php if ($_SESSION['perfil']!='adm') { echo "disabled=\"disabled\""; } ?> >
				<input type="button" value="Pesquisa" onclick="lista();">
				<input type="text" value="" id="edtPesquisa" onkeydown="lista();">