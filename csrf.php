<?php
// Validação de token CSRF. Inclua no início de todo endpoint que ALTERA
// estado (grava/apaga/inclui/limpa). O token vem em $_REQUEST['csrf'] e é
// comparado com o da sessão (gerado em template/start.php).
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
$tokenRecebido = $_REQUEST['csrf'] ?? '';
if (empty($_SESSION['csrf']) || !is_string($tokenRecebido) || !hash_equals($_SESSION['csrf'], $tokenRecebido)) {
	header('HTTP/1.1 403 Forbidden');
	echo "<h1>403 - Token CSRF inválido</h1>";
	echo "Recarregue a página e tente novamente.";
	die();
}
?>
