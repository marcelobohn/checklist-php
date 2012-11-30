<?php
	echo "<label>Cliente: </label><input type=\"text\" id=\"cliente\"><br />";
	echo "<label>Tarefa: </label><input type=\"text\" id=\"tarefa\"><br />";
	echo "<label>&nbsp;</label><input type=\"button\" value=\"Filtrar\" onclick=\"filtro()\"><br />";
	echo "<div id=\"filtro\">";
	include('consulta.lista.filtro.php');
	echo"</div>";
	echo "<div id=\"registro\"></div>";
	
?>	