<?php

declare(strict_types=1);

// Helpers globais carregados via Composer ("files" autoload).

// Escape de saída (prevenção de XSS). Use ao imprimir qualquer valor de TEXTO
// LIVRE vindo do banco ou do usuário dentro de HTML/atributos.
//
// Convenção do projeto: valores estruturalmente seguros são impressos crus de
// propósito — IDs inteiros (AUTO_INCREMENT) e flags 'S'/'N' (marcar, resposta,
// admin), que nunca contêm < ou ". Todo campo digitável, porém, passa por h().
// Ver CLAUDE.md → "Segurança / dívida técnica".
if (!function_exists('h')) {
	function h($v): string {
		return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
	}
}
