<?php

declare(strict_types=1);

// Helpers globais carregados via Composer ("files" autoload).

// Escape de saída (prevenção de XSS). Use ao imprimir qualquer valor vindo do
// banco ou do usuário dentro de HTML/atributos.
if (!function_exists('h')) {
	function h($v): string {
		return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
	}
}
