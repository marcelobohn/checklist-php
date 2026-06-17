<?php
require_once __DIR__ . '/vendor/autoload.php';
session_start();
$bd = new \App\ConexaoBD();

$sql = "select * from usuario where nome = ?";
$stmt = $bd->query( $sql, array( $_POST['usuario'] ) );
$r = $stmt->fetch();
if( $r )	{
	if (password_verify($_POST['senha'], $r['senha'])) {
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