<?php
// Controle de acesso por sessão.
//
// Substitui o antigo anti-hotlink baseado em HTTP_REFERER (forjável e
// frequentemente ausente). Inclua este arquivo no topo de toda página/endpoint
// que exija autenticação — ele bloqueia o acesso quando não há login ativo.
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
if (($_SESSION['modo'] ?? '') !== 'de') {
	header('HTTP/1.1 403 Forbidden');
	echo "<h1>403 - Acesso negado</h1>";
	echo "Faça login para acessar esta página.";
	die();
}
?>
