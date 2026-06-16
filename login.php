<?php
session_start();
include_once ("conexaoBD.php");
$bd = new conexaoBD();

$sql = "select * from usuario where nome = ?";
$stmt = $bd->query( $sql, array( $_POST['usuario'] ) );
$r = $stmt->fetch();
if( $r )	{
	if ($r['senha'] == $_POST['senha']) {
		$_SESSION['usuario'] = $r['nome'];
		$_SESSION['modo'] = 'de';
		if ($r['admin'] == 'S') {
			$_SESSION['perfil'] = 'adm';
		} else {
			$_SESSION['perfil'] = 'usr';
		}
		header( 'Location: index.php' );
	} else {
		echo "Verifique o Usuário e Senha";
		echo "<script type=\"text/javascript\">";
		echo "function espera(){ ";
		echo "window.location = \"index.php\" } ";
		echo "setTimeout('espera()', 2000)";
		echo "</script>";
	}
}
?>