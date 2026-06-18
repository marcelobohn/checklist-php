<?php

declare(strict_types=1);

namespace App;

/**
 * Despacha a ação de um endpoint a partir do parâmetro `?acao=`.
 *
 * Centraliza o "se acao==X faça Y" que antes ficava repetido (e espalhado) em
 * cada `<mod>.model.php`. Registre os handlers com `on()` e dispare com `run()`
 * passando o valor de `$_REQUEST['acao']`. Ação ausente ou desconhecida é um
 * no-op — o mesmo comportamento dos antigos `if ($acao=='...')` soltos.
 *
 *     (new Dispatcher())
 *         ->on('grava', function () { ... })
 *         ->on('apaga', function () { ... })
 *         ->run($_REQUEST['acao'] ?? null);
 */
final class Dispatcher
{
	/** @var array<string, callable> */
	private array $acoes = array();

	/** Registra o handler de uma ação (encadeável). */
	public function on(string $acao, callable $handler): self
	{
		$this->acoes[$acao] = $handler;
		return $this;
	}

	/** Executa o handler da ação informada; ação ausente/desconhecida não faz nada. */
	public function run(?string $acao): void
	{
		if ($acao !== null && isset($this->acoes[$acao])) {
			($this->acoes[$acao])();
		}
	}
}
