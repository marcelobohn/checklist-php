<?php
// Controle de acesso por sessão.
//
// Substitui o antigo anti-hotlink baseado em HTTP_REFERER (forjável e
// frequentemente ausente). Inclua este arquivo no topo de toda página/endpoint
// que exija autenticação — ele bloqueia o acesso quando não há login ativo.
//
// Também é o ponto único de bootstrap do autoloader do Composer para os
// endpoints autenticados (classes em src/App via PSR-4).
require_once __DIR__ . '/vendor/autoload.php';
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
